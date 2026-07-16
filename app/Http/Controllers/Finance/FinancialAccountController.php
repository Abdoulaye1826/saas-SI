<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFinancialAccountRequest;
use App\Models\FinancialAccount;
use App\Services\FinancialAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FinancialAccountController extends Controller
{
    public function __construct(private readonly FinancialAccountService $accountService)
    {
    }

    public function index(): View
    {
        $accounts = $this->accountService->all();

        return view('finance.accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('finance.accounts.create');
    }

    public function store(StoreFinancialAccountRequest $request): RedirectResponse
    {
        $this->accountService->create($request->validated());

        return redirect()->route('finance.accounts.index')->with('success', 'Compte financier créé avec succès.');
    }

    public function edit(FinancialAccount $account): View
    {
        return view('finance.accounts.edit', compact('account'));
    }

    public function update(StoreFinancialAccountRequest $request, FinancialAccount $account): RedirectResponse
    {
        $this->accountService->update($account, $request->validated());

        return redirect()->route('finance.accounts.index')->with('success', 'Compte financier mis à jour avec succès.');
    }

    public function destroy(FinancialAccount $account): RedirectResponse
    {
        try {
            $this->accountService->delete($account);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('finance.accounts.index')->with('success', 'Compte financier supprimé avec succès.');
    }
}
