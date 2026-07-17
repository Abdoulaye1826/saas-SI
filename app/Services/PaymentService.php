<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

/**
 * Traçabilité réelle des paiements de factures (Wave, Orange Money, Espèces).
 * Le statut de la facture est recalculé automatiquement à chaque
 * enregistrement ou suppression de paiement. Chaque paiement génère aussi
 * une entrée de trésorerie automatique (voir TreasuryService) — vente
 * initiale ou paiement complémentaire ultérieur.
 */
class PaymentService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
        private readonly TreasuryService $treasuryService
    ) {
    }

    /**
     * @param string|null $category Catégorie de trésorerie explicite ('vente'/'echange'
     *   passée par SaleService) ; si null, déduite (paiement_facture / paiement_complementaire).
     */
    public function store(Invoice $invoice, array $data, int $userId, ?string $category = null): Payment
    {
        if ($invoice->status === InvoiceStatus::Cancelled) {
            throw new \RuntimeException('Impossible d\'enregistrer un paiement sur une facture annulée.');
        }

        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw new \RuntimeException('Le montant doit être supérieur à 0.');
        }

        $remaining = $invoice->remaining_amount;
        if ($amount > $remaining + 0.01) {
            throw new \RuntimeException(
                'Le montant dépasse le reste à payer (' . number_format($remaining, 0, ',', ' ') . ' FCFA).'
            );
        }

        return DB::transaction(function () use ($invoice, $data, $userId, $amount, $category) {
            $isFirstPayment = $invoice->payments()->count() === 0;

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'method' => $data['method'],
                'paid_at' => $data['paid_at'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'recorded_by' => $userId,
            ]);

            $this->syncInvoiceStatus($invoice);

            $this->activityLog->log(
                'payment',
                $payment,
                "Paiement enregistré : " . number_format($amount, 0, ',', ' ') . " FCFA ({$payment->method->label()}) sur facture {$invoice->invoice_number}"
            );

            $this->treasuryService->createAutoEntry([
                'category' => $category ?? ($isFirstPayment ? 'paiement_facture' : 'paiement_complementaire'),
                'amount' => $amount,
                'date' => $data['paid_at'],
                'reference' => $invoice->invoice_number,
                'description' => "Paiement facture {$invoice->invoice_number}",
                'payment_id' => $payment->id,
            ], $userId);

            return $payment;
        });
    }

    public function destroy(Payment $payment): void
    {
        $invoice = $payment->invoice;
        $amount = (float) $payment->amount;
        $method = $payment->method->label();

        DB::transaction(function () use ($payment, $invoice) {
            $this->treasuryService->reverseAutoEntry($payment);
            $payment->delete();
            $this->syncInvoiceStatus($invoice);
        });

        $this->activityLog->log(
            'payment_delete',
            null,
            "Paiement supprimé : " . number_format($amount, 0, ',', ' ') . " FCFA ({$method}) sur facture {$invoice->invoice_number}"
        );
    }

    private function syncInvoiceStatus(Invoice $invoice): void
    {
        $invoice = $invoice->fresh('payments');

        if ($invoice->status === InvoiceStatus::Cancelled) {
            return;
        }

        if ($invoice->isFullyPaid()) {
            $invoice->update(['status' => InvoiceStatus::Paid]);
        } elseif ($invoice->amount_paid > 0) {
            $invoice->update(['status' => InvoiceStatus::Partial]);
        } else {
            $invoice->update(['status' => InvoiceStatus::Issued]);
        }
    }
}
