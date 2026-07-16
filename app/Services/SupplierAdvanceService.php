<?php

namespace App\Services;

use App\Enums\FinancialCategory;
use App\Enums\FinancialTransactionType;
use App\Models\SupplierAdvance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SupplierAdvanceService
{
    public function __construct(
        private readonly FinancialTransactionService $transactionService,
        private readonly ActivityLogService $activityLog
    ) {
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return SupplierAdvance::query()
            ->with(['supplier', 'account', 'user'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): SupplierAdvance
    {
        return DB::transaction(function () use ($data, $userId) {
            $advance = SupplierAdvance::create($data + ['user_id' => $userId, 'amount_used' => 0]);

            $this->transactionService->createAuto([
                'financial_account_id' => $advance->financial_account_id,
                'type' => FinancialTransactionType::Out->value,
                'category' => FinancialCategory::AvanceFournisseur->value,
                'amount' => $advance->amount,
                'date' => $advance->date,
                'supplier_id' => $advance->supplier_id,
                'reference' => $advance->reference,
                'description' => "Avance versée à {$advance->supplier->name}",
            ], $userId, $advance);

            $this->activityLog->log(
                'create',
                $advance,
                "Avance fournisseur enregistrée : {$advance->supplier->name} — " . number_format((float) $advance->amount, 0, ',', ' ') . ' FCFA'
            );

            return $advance;
        });
    }
}
