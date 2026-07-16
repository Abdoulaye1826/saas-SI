<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternalTransferRequest;
use App\Services\FinancialAccountService;
use App\Services\InternalTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InternalTransferController extends Controller
{
    public function __construct(
        private readonly InternalTransferService $transferService,
        private readonly FinancialAccountService $accountService
    ) {
    }

    public function index(): View
    {
        $transfers = $this->transferService->paginate();

        return view('finance.transfers.index', compact('transfers'));
    }

    public function create(): View
    {
        $accounts = $this->accountService->active();

        return view('finance.transfers.create', compact('accounts'));
    }

    public function store(StoreInternalTransferRequest $request): RedirectResponse
    {
        try {
            $this->transferService->create($request->validated(), auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('finance.transfers.index')->with('success', 'Virement effectué avec succès.');
    }
}
