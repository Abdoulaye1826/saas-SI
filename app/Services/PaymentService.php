<?php

namespace App\Services;

use App\Enums\FinancialCategory;
use App\Enums\FinancialTransactionType;
use App\Enums\InvoiceStatus;
use App\Models\FinancialAccount;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

/**
 * Traçabilité réelle des paiements de factures (Wave, Orange Money, Espèces).
 * Le statut de la facture est recalculé automatiquement à chaque
 * enregistrement ou suppression de paiement. Chaque paiement (hors ceux
 * financés par une avance client déjà encaissée) génère aussi une écriture
 * de trésorerie automatique sur le compte correspondant au mode de paiement
 * — voir FinancialTransactionService.
 */
class PaymentService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
        private readonly FinancialTransactionService $transactionService
    ) {
    }

    /**
     * @param array $data ['amount','method','paid_at','reference','notes','client_advance_id']
     * @param string|null $category Catégorie de trésorerie à utiliser explicitement (ex: 'vente'/'echange'
     *   passé par SaleService) ; si null, déduite automatiquement (paiement_facture / paiement_complementaire).
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
            $fundedByAdvance = ! empty($data['client_advance_id']);

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'client_advance_id' => $data['client_advance_id'] ?? null,
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

            if (! $fundedByAdvance) {
                $this->recordTreasuryEntry($payment, $invoice, $category ?? ($isFirstPayment ? 'paiement_facture' : 'paiement_complementaire'), $userId);
            }

            return $payment;
        });
    }

    public function destroy(Payment $payment): void
    {
        $invoice = $payment->invoice;
        $amount = (float) $payment->amount;
        $method = $payment->method->label();

        DB::transaction(function () use ($payment, $invoice) {
            $this->transactionService->reverseAutoFor($payment);
            $payment->delete();
            $this->syncInvoiceStatus($invoice);
        });

        $this->activityLog->log(
            'payment_delete',
            null,
            "Paiement supprimé : " . number_format($amount, 0, ',', ' ') . " FCFA ({$method}) sur facture {$invoice->invoice_number}"
        );
    }

    public function syncInvoiceStatus(Invoice $invoice): void
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

    /**
     * Crée l'écriture de trésorerie correspondant au mode de paiement choisi
     * (Wave -> compte Wave, Espèces -> Caisse...). Ne fait rien silencieusement
     * si aucun compte n'est configuré pour ce mode : le module Finance est
     * additif, son absence de configuration ne doit jamais bloquer une vente.
     */
    private function recordTreasuryEntry(Payment $payment, Invoice $invoice, string $category, int $userId): void
    {
        $account = FinancialAccount::forPaymentMethod($payment->method);
        if ($account === null) {
            return;
        }

        $this->transactionService->createAuto([
            'financial_account_id' => $account->id,
            'type' => FinancialTransactionType::In->value,
            'category' => $category,
            'amount' => $payment->amount,
            'date' => $payment->paid_at,
            'customer_id' => $invoice->customer_id,
            'reference' => $invoice->invoice_number,
            'description' => "Paiement facture {$invoice->invoice_number}",
        ], $userId, $payment);
    }
}
