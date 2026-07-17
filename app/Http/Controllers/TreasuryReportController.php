<?php

namespace App\Http\Controllers;

use App\Services\TreasuryService;
use App\Support\DashboardPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TreasuryReportController extends Controller
{
    public function __construct(private readonly TreasuryService $treasuryService)
    {
    }

    public function index(Request $request): View
    {
        $period = DashboardPeriod::fromRequest($request);
        $report = $this->treasuryService->getReport($period);

        return view('treasury.reports.index', compact('period', 'report'));
    }

    public function pdf(Request $request): Response
    {
        $period = DashboardPeriod::fromRequest($request);
        $pdfContent = $this->treasuryService->renderReportPdf($period);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport-tresorerie.pdf"',
        ]);
    }
}
