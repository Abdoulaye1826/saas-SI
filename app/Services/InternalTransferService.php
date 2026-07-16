<?php

namespace App\Services;

use App\Enums\FinancialCategory;
use App\Enums\FinancialTransactionType;
use App\Models\InternalTransfer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InternalTransferService
{
    public function __construct(
        private readonly FinancialTransactionService $transactionService,
        private readonly ActivityLogService $activityLog
    ) {
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return InternalTransfer::query()
            ->with(['fromAccount', 'toAccount', 'user'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): InternalTransfer
    {
        if ($data['from_account_id'] === $data['to_account_id']) {
            throw new \RuntimeException('Le compte source et le compte destination doivent être différents.');
        }

        return DB::transaction(function () use ($data, $userId) {
            $transfer = InternalTransfer::create([
                'from_account_id' => $data['from_account_id'],
                'to_account_id' => $data['to_account_id'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'reason' => $data['reason'] ?? null,
                'user_id' => $userId,
            ]);

            $this->transactionService->createAuto([
                'financial_account_id' => $transfer->from_account_id,
                'type' => FinancialTransactionType::Out->value,
                'category' => FinancialCategory::Virement->value,
                'amount' => $transfer->amount,
                'date' => $transfer->date,
                'description' => "Virement vers {$transfer->toAccount->name}" . ($transfer->reason ? " — {$transfer->reason}" : ''),
            ], $userId, $transfer);

            $this->transactionService->createAuto([
                'financial_account_id' => $transfer->to_account_id,
                'type' => FinancialTransactionType::In->value,
                'category' => FinancialCategory::Virement->value,
                'amount' => $transfer->amount,
                'date' => $transfer->date,
                'description' => "Virement depuis {$transfer->fromAccount->name}" . ($transfer->reason ? " — {$transfer->reason}" : ''),
            ], $userId, $transfer);

            $this->activityLog->log(
                'create',
                $transfer,
                "Virement de {$transfer->fromAccount->name} vers {$transfer->toAccount->name} : " . number_format((float) $transfer->amount, 0, ',', ' ') . ' FCFA'
            );

            return $transfer;
        });
    }
}
