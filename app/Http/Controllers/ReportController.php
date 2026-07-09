<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Support\DashboardPeriod;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index(): View
    {
        return view('reports.index', [
            'stats' => $this->dashboardService->getStats(DashboardPeriod::allTime()),
            'salesByMonth' => $this->dashboardService->getSalesByMonth(),
            'salesByCategory' => $this->dashboardService->getSalesByCategory(),
            'invoiceStatusSummary' => $this->dashboardService->getInvoiceStatusSummary(),
            'topProducts' => $this->dashboardService->getTopProducts(),
            'topCustomers' => $this->dashboardService->getTopCustomers(),
            'salesByUser' => $this->dashboardService->getSalesByUser(),
            'recentInvoices' => $this->dashboardService->getRecentInvoices(),
        ]);
    }
}
