<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Support\DashboardPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index(Request $request): View
    {
        // Par défaut "toutes les périodes" (comportement historique de cette
        // page) si aucun filtre n'est explicitement choisi.
        if (!$request->has('period')) {
            $request->merge(['period' => 'all']);
        }

        $period = DashboardPeriod::fromRequest($request);

        return view('reports.index', [
            'period' => $period,
            'stats' => $this->dashboardService->getStats($period),
            'salesByMonth' => $this->dashboardService->getSalesByMonth(),
            'salesByCategory' => $this->dashboardService->getSalesByCategory($period),
            'invoiceStatusSummary' => $this->dashboardService->getInvoiceStatusSummary($period),
            'topProducts' => $this->dashboardService->getTopProducts(5, $period),
            'topCustomers' => $this->dashboardService->getTopCustomers(5, $period),
            'salesByUser' => $this->dashboardService->getSalesByUser(5, $period),
            'salesTypeBreakdown' => $this->dashboardService->getSalesTypeBreakdown($period),
            'recentInvoices' => $this->dashboardService->getRecentInvoices(),
            'recentQuotes' => $this->dashboardService->getRecentQuotes(),
            'recentStockMovements' => $this->dashboardService->getRecentStockMovements(),
        ]);
    }
}
