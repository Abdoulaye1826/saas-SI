<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierAdvanceRequest;
use App\Models\Supplier;
use App\Services\FinancialAccountService;
use App\Services\SupplierAdvanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierAdvanceController extends Controller
{
    public function __construct(
        private readonly SupplierAdvanceService $advanceService,
        private readonly FinancialAccountService $accountService
    ) {
    }

    public function index(): View
    {
        $advances = $this->advanceService->paginate();

        return view('finance.supplier-advances.index', compact('advances'));
    }

    public function create(): View
    {
        $suppliers = Supplier::query()->orderBy('name')->get();
        $accounts = $this->accountService->active();

        return view('finance.supplier-advances.create', compact('suppliers', 'accounts'));
    }

    public function store(StoreSupplierAdvanceRequest $request): RedirectResponse
    {
        $this->advanceService->create($request->validated(), auth()->id());

        return redirect()->route('finance.supplier-advances.index')->with('success', 'Avance fournisseur enregistrée avec succès.');
    }
}
