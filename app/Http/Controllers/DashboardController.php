<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Support\DashboardPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $period = DashboardPeriod::fromRequest($request);
        $isCashier = auth()->user()->hasRole('cashier');

        $data = [
            'period' => $period,
            'isCashier' => $isCashier,
            'stats' => $this->dashboardService->getStats($period),
            'salesEvolution' => $this->dashboardService->getSalesEvolution($period),
            'salesByCategory' => $this->dashboardService->getSalesByCategory($period),
            'invoiceStatusSummary' => $this->dashboardService->getInvoiceStatusSummary($period),
            'topProducts' => $this->dashboardService->getTopProducts(5, $period),
            'topCustomers' => $this->dashboardService->getTopCustomers(5, $period),
            'salesByUser' => $this->dashboardService->getSalesByUser(5, $period),
            'recentInvoices' => $this->dashboardService->getRecentInvoices(),
            'recentQuotes' => $this->dashboardService->getRecentQuotes(),
            'stockAlerts' => $this->dashboardService->getStockAlerts(),
            'salesTypeBreakdown' => $this->dashboardService->getSalesTypeBreakdown($period),
            'recentStockMovements' => $this->dashboardService->getRecentStockMovements(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'kpisHtml' => view('dashboard.partials.kpis', $data)->render(),
                'tablesHtml' => view('dashboard.partials.tables', $data)->render(),
                'charts' => [
                    'salesEvolution' => $data['salesEvolution'],
                    'salesByCategory' => $data['salesByCategory'],
                    'invoiceStatusSummary' => $data['invoiceStatusSummary'],
                    'salesTypeBreakdown' => $data['salesTypeBreakdown'],
                ],
                'period' => [
                    'key' => $period->key,
                    'label' => $period->label,
                    'start' => $period->start->toDateString(),
                    'end' => $period->end->toDateString(),
                ],
            ]);
        }

        return view('dashboard.index', $data);
    }
}
