<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\FinanceDashboardService;
use App\Support\DashboardPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialDashboardController extends Controller
{
    public function __construct(private readonly FinanceDashboardService $dashboardService)
    {
    }

    public function index(Request $request): View
    {
        $period = DashboardPeriod::fromRequest($request);
        $kpis = $this->dashboardService->getKpis($period);
        $charts = $this->dashboardService->getCharts($period);

        return view('finance.dashboard', compact('period', 'kpis', 'charts'));
    }
}
