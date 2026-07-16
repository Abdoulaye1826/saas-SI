<?php

namespace App\Policies;

use App\Models\FinancialTransaction;
use App\Models\User;

/**
 * Un caissier peut voir et saisir des écritures de trésorerie, mais ne peut
 * ni les modifier ni les supprimer — seuls admin/manager le peuvent.
 */
class FinancialTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isCashier();
    }

    public function view(User $user, FinancialTransaction $transaction): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isCashier();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isCashier();
    }

    public function update(User $user, FinancialTransaction $transaction): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, FinancialTransaction $transaction): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
