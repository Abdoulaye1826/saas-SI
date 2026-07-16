<?php

namespace App\Services;

use App\Enums\FinancialTransactionType;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Coeur du module Trésorerie : chaque écriture (manuelle ou générée
 * automatiquement par une vente/un paiement/une avance/un virement) passe
 * par ce service, qui est le seul endroit où le solde d'un compte est
 * modifié — jamais de recalcul par SUM() à l'affichage, jamais d'écriture
 * du solde ailleurs que dans applyBalance()/reverseBalance() ci-dessous.
 */
class FinancialTransactionService
{
    public function __construct(private readonly ActivityLogService $activityLog)
    {
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return FinancialTransaction::query()
            ->with(['account', 'supplier', 'customer', 'user'])
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['category'] ?? null, fn ($q, $cat) => $q->where('category', $cat))
            ->when($filters['financial_account_id'] ?? null, fn ($q, $id) => $q->where('financial_account_id', $id))
            ->when($filters['start'] ?? null, fn ($q, $start) => $q->whereDate('date', '>=', $start))
            ->when($filters['end'] ?? null, fn ($q, $end) => $q->whereDate('date', '<=', $end))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data, int $userId, ?UploadedFile $attachment = null): FinancialTransaction
    {
        return DB::transaction(function () use ($data, $userId, $attachment) {
            $data['user_id'] = $userId;
            $data['is_auto_generated'] = false;

            if ($attachment) {
                $data['attachment_path'] = $attachment->store('finance', 'public');
            }

            $transaction = FinancialTransaction::create($data);
            $this->applyBalance($transaction->account, $transaction->type, (float) $transaction->amount);

            $this->activityLog->log(
                'create',
                $transaction,
                "Écriture créée : {$transaction->type->label()} de " . number_format((float) $transaction->amount, 0, ',', ' ') . " FCFA ({$transaction->category->label()})",
                null,
                $transaction->only(['financial_account_id', 'type', 'category', 'amount', 'date', 'reference', 'description'])
            );

            return $transaction;
        });
    }

    /**
     * Point d'entrée utilisé par les intégrations automatiques (paiement,
     * avance, virement...). Pas de justificatif, pas de vérification de
     * saisie utilisateur : les données proviennent d'une opération déjà
     * validée ailleurs dans l'application.
     */
    public function createAuto(array $data, int $userId, ?Model $related = null): FinancialTransaction
    {
        return DB::transaction(function () use ($data, $userId, $related) {
            $data['user_id'] = $userId;
            $data['is_auto_generated'] = true;

            if ($related) {
                $data['related_type'] = $related::class;
                $data['related_id'] = $related->getKey();
            }

            $transaction = FinancialTransaction::create($data);
            $this->applyBalance($transaction->account, $transaction->type, (float) $transaction->amount);

            $this->activityLog->log(
                'create_auto',
                $transaction,
                "Écriture automatique : {$transaction->type->label()} de " . number_format((float) $transaction->amount, 0, ',', ' ') . " FCFA ({$transaction->category->label()})"
            );

            return $transaction;
        });
    }

    public function update(FinancialTransaction $transaction, array $data, int $userId, ?UploadedFile $attachment = null): FinancialTransaction
    {
        return DB::transaction(function () use ($transaction, $data, $userId, $attachment) {
            $oldValues = $transaction->only(['financial_account_id', 'type', 'category', 'amount', 'date', 'reference', 'description']);
            $oldAccount = $transaction->account;
            $oldType = $transaction->type;
            $oldAmount = (float) $transaction->amount;

            if ($attachment) {
                if ($transaction->attachment_path) {
                    Storage::disk('public')->delete($transaction->attachment_path);
                }
                $data['attachment_path'] = $attachment->store('finance', 'public');
            }

            $data['updated_by'] = $userId;
            $transaction->update($data);
            $transaction->refresh();

            // Annule l'effet de l'ancienne écriture sur l'ancien compte, puis
            // applique la nouvelle sur le compte (éventuellement différent).
            $this->reverseBalance($oldAccount, $oldType, $oldAmount);
            $this->applyBalance($transaction->account, $transaction->type, (float) $transaction->amount);

            $this->activityLog->log(
                'update',
                $transaction,
                "Écriture modifiée : {$transaction->category->label()}",
                $oldValues,
                $transaction->only(['financial_account_id', 'type', 'category', 'amount', 'date', 'reference', 'description'])
            );

            return $transaction;
        });
    }

    public function delete(FinancialTransaction $transaction, int $userId): void
    {
        DB::transaction(function () use ($transaction, $userId) {
            $this->reverseBalance($transaction->account, $transaction->type, (float) $transaction->amount);

            $transaction->update(['updated_by' => $userId]);
            $transaction->delete();

            $this->activityLog->log(
                'delete',
                $transaction,
                "Écriture supprimée : {$transaction->type->label()} de " . number_format((float) $transaction->amount, 0, ',', ' ') . " FCFA ({$transaction->category->label()})"
            );
        });
    }

    /**
     * Annule la ou les écritures automatiques liées à un modèle donné (ex :
     * un Payment supprimé) — utilisé par PaymentService::destroy().
     */
    public function reverseAutoFor(Model $related): void
    {
        $transactions = FinancialTransaction::query()
            ->where('related_type', $related::class)
            ->where('related_id', $related->getKey())
            ->get();

        foreach ($transactions as $transaction) {
            $this->reverseBalance($transaction->account, $transaction->type, (float) $transaction->amount);
            $transaction->delete();
        }
    }

    /**
     * Solde d'un compte juste avant le début d'une date donnée — reconstruit
     * à partir des écritures antérieures plutôt que déduit de
     * current_balance (qui reflète l'instant présent, pas une date passée).
     * Utilisé par le journal de caisse.
     */
    public function openingBalance(FinancialAccount $account, \DateTimeInterface $date): float
    {
        $in = (float) FinancialTransaction::query()->forAccount($account->id)->in()
            ->whereDate('date', '<', $date)->sum('amount');
        $out = (float) FinancialTransaction::query()->forAccount($account->id)->out()
            ->whereDate('date', '<', $date)->sum('amount');

        return $in - $out;
    }

    private function applyBalance(FinancialAccount $account, FinancialTransactionType $type, float $amount): void
    {
        if ($type === FinancialTransactionType::In) {
            $account->increment('current_balance', $amount);
        } else {
            $account->decrement('current_balance', $amount);
        }
    }

    private function reverseBalance(FinancialAccount $account, FinancialTransactionType $type, float $amount): void
    {
        if ($type === FinancialTransactionType::In) {
            $account->decrement('current_balance', $amount);
        } else {
            $account->increment('current_balance', $amount);
        }
    }
}
