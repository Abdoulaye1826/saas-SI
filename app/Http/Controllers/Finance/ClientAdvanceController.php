<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyClientAdvanceRequest;
use App\Http\Requests\StoreClientAdvanceRequest;
use App\Models\ClientAdvance;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\ClientAdvanceService;
use App\Services\FinancialAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientAdvanceController extends Controller
{
    public function __construct(
        private readonly ClientAdvanceService $advanceService,
        private readonly FinancialAccountService $accountService
    ) {
    }

    public function index(): View
    {
        $advances = $this->advanceService->paginate();

        return view('finance.client-advances.index', compact('advances'));
    }

    public function create(): View
    {
        $customers = Customer::query()->orderBy('full_name')->get();
        $accounts = $this->accountService->active();

        return view('finance.client-advances.create', compact('customers', 'accounts'));
    }

    public function store(StoreClientAdvanceRequest $request): RedirectResponse
    {
        $this->advanceService->create($request->validated(), auth()->id());

        return redirect()->route('finance.client-advances.index')->with('success', 'Avance client enregistrée avec succès.');
    }

    /**
     * Applique une avance sur une facture du même client (formulaire simple
     * intégré à la page de détail de l'avance).
     */
    public function apply(ApplyClientAdvanceRequest $request, ClientAdvance $clientAdvance): RedirectResponse
    {
        $invoice = Invoice::where('customer_id', $clientAdvance->customer_id)->findOrFail($request->input('invoice_id'));

        try {
            $this->advanceService->applyToInvoice($clientAdvance, $invoice, (float) $request->input('amount'), auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('finance.client-advances.index')->with('success', 'Avance appliquée à la facture avec succès.');
    }
}
