<?php

namespace App\Services;

use App\Enums\FinancialCategory;
use App\Enums\FinancialTransactionType;
use App\Models\ClientAdvance;
use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientAdvanceService
{
    public function __construct(
        private readonly FinancialTransactionService $transactionService,
        private readonly ActivityLogService $activityLog
    ) {
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return ClientAdvance::query()
            ->with(['customer', 'account', 'user'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): ClientAdvance
    {
        return DB::transaction(function () use ($data, $userId) {
            $advance = ClientAdvance::create($data + ['user_id' => $userId, 'amount_used' => 0]);

            $this->transactionService->createAuto([
                'financial_account_id' => $advance->financial_account_id,
                'type' => FinancialTransactionType::In->value,
                'category' => FinancialCategory::AvanceClient->value,
                'amount' => $advance->amount,
                'date' => $advance->date,
                'customer_id' => $advance->customer_id,
                'reference' => $advance->reference,
                'description' => "Avance de {$advance->customer->full_name}",
            ], $userId, $advance);

            $this->activityLog->log(
                'create',
                $advance,
                "Avance client enregistrée : {$advance->customer->full_name} — " . number_format((float) $advance->amount, 0, ',', ' ') . ' FCFA'
            );

            return $advance;
        });
    }

    /**
     * Applique tout ou partie d'une avance à une facture : crée un Payment
     * (donc met à jour amount_paid/remaining_amount de la facture) SANS
     * générer de nouvelle écriture de trésorerie — l'argent est déjà rentré
     * au moment de l'avance, une 2ᵉ écriture ferait un double comptage.
     */
    public function applyToInvoice(ClientAdvance $advance, Invoice $invoice, float $amount, int $userId): void
    {
        if ($amount > $advance->remaining_amount + 0.01) {
            throw new \RuntimeException('Le montant dépasse le solde disponible de cette avance.');
        }

        DB::transaction(function () use ($advance, $invoice, $amount, $userId) {
            $invoice->payments()->create([
                'client_advance_id' => $advance->id,
                'amount' => $amount,
                'method' => $advance->payment_method->value,
                'paid_at' => now()->toDateString(),
                'reference' => $advance->reference,
                'notes' => "Réglé via l'avance client du " . $advance->date->format('d/m/Y'),
                'recorded_by' => $userId,
            ]);

            $advance->increment('amount_used', $amount);

            app(PaymentService::class)->syncInvoiceStatus($invoice);

            $this->activityLog->log(
                'apply_advance',
                $advance,
                "Avance appliquée sur la facture {$invoice->invoice_number} : " . number_format($amount, 0, ',', ' ') . ' FCFA'
            );
        });
    }
}
