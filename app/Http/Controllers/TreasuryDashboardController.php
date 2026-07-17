<?php

namespace App\Http\Controllers;

use App\Services\TreasuryService;
use App\Support\DashboardPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreasuryDashboardController extends Controller
{
    public function __construct(private readonly TreasuryService $treasuryService)
    {
    }

    public function index(Request $request): View
    {
        if (! $request->has('period')) {
            $request->merge(['period' => 'today']);
        }

        $period = DashboardPeriod::fromRequest($request);
        $stats = $this->treasuryService->getDashboard($period);

        return view('treasury.dashboard', compact('period', 'stats'));
    }
}
