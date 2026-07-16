<?php

namespace App\Http\Controllers\Finance;

use App\Enums\FinancialCategory;
use App\Enums\FinancialTransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFinancialTransactionRequest;
use App\Models\FinancialTransaction;
use App\Services\FinancialAccountService;
use App\Services\FinancialTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Entrées et sorties d'argent partagent ce même contrôleur (une seule
 * table `financial_transactions`, distinguées par `type`) — le paramètre
 * de requête `type=in|out` détermine quelle vue du menu est active.
 */
class FinancialTransactionController extends Controller
{
    public function __construct(
        private readonly FinancialTransactionService $transactionService,
        private readonly FinancialAccountService $accountService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', FinancialTransaction::class);

        $type = $request->query('type', 'in') === 'out' ? 'out' : 'in';
        $filters = $request->only(['category', 'financial_account_id', 'start', 'end', 'search']) + ['type' => $type];
        $transactions = $this->transactionService->paginate($filters);
        $accounts = $this->accountService->active();
        $categories = FinancialCategory::forType(FinancialTransactionType::from($type));

        return view('finance.transactions.index', compact('transactions', 'accounts', 'categories', 'type', 'filters'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', FinancialTransaction::class);

        $type = $request->query('type', 'in') === 'out' ? 'out' : 'in';
        $accounts = $this->accountService->active();
        $categories = FinancialCategory::forType(FinancialTransactionType::from($type));

        return view('finance.transactions.create', compact('type', 'accounts', 'categories'));
    }

    public function store(StoreFinancialTransactionRequest $request): RedirectResponse
    {
        $this->authorize('create', FinancialTransaction::class);

        $this->transactionService->create($request->validated(), auth()->id(), $request->file('attachment'));

        return redirect()->route('finance.transactions.index', ['type' => $request->input('type')])
            ->with('success', 'Écriture enregistrée avec succès.');
    }

    public function edit(FinancialTransaction $transaction): View
    {
        $this->authorize('update', $transaction);

        $accounts = $this->accountService->active();
        $categories = FinancialCategory::forType($transaction->type);

        return view('finance.transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(StoreFinancialTransactionRequest $request, FinancialTransaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $this->transactionService->update($transaction, $request->validated(), auth()->id(), $request->file('attachment'));

        return redirect()->route('finance.transactions.index', ['type' => $transaction->type->value])
            ->with('success', 'Écriture modifiée avec succès.');
    }

    public function destroy(FinancialTransaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $type = $transaction->type->value;
        $this->transactionService->delete($transaction, auth()->id());

        return redirect()->route('finance.transactions.index', ['type' => $type])
            ->with('success', 'Écriture supprimée avec succès.');
    }
}
