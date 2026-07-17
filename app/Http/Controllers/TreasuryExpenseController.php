<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreasuryExpenseRequest;
use App\Services\TreasuryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TreasuryExpenseController extends Controller
{
    public function __construct(private readonly TreasuryService $treasuryService)
    {
    }

    public function create(): View
    {
        return view('treasury.expenses.create');
    }

    public function store(StoreTreasuryExpenseRequest $request): RedirectResponse
    {
        $this->treasuryService->createExpense($request->validated(), auth()->id());

        return redirect()->route('treasury.history.index')->with('success', 'Dépense enregistrée avec succès.');
    }
}
