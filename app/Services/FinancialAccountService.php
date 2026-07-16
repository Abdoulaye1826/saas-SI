<?php

namespace App\Services;

use App\Models\FinancialAccount;
use Illuminate\Support\Collection;

class FinancialAccountService
{
    public function __construct(private readonly ActivityLogService $activityLog)
    {
    }

    public function all(): Collection
    {
        return FinancialAccount::query()->orderBy('name')->get();
    }

    public function active(): Collection
    {
        return FinancialAccount::query()->active()->orderBy('name')->get();
    }

    public function create(array $data): FinancialAccount
    {
        $account = FinancialAccount::create($data);
        $this->activityLog->log('create', $account, "Compte financier créé : {$account->name}");

        return $account;
    }

    public function update(FinancialAccount $account, array $data): FinancialAccount
    {
        $account->update($data);
        $this->activityLog->log('update', $account, "Compte financier modifié : {$account->name}");

        return $account->fresh();
    }

    public function delete(FinancialAccount $account): void
    {
        if ($account->transactions()->exists()) {
            throw new \RuntimeException('Impossible de supprimer un compte ayant des écritures : désactivez-le plutôt.');
        }

        $name = $account->name;
        $account->delete();
        $this->activityLog->log('delete', null, "Compte financier supprimé : {$name}");
    }
}
