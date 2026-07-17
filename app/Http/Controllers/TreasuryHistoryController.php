<?php

namespace App\Http\Controllers;

use App\Services\TreasuryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreasuryHistoryController extends Controller
{
    public function __construct(private readonly TreasuryService $treasuryService)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['type', 'start', 'end']);
        $transactions = $this->treasuryService->paginateHistory($filters);

        return view('treasury.history.index', compact('transactions', 'filters'));
    }
}
