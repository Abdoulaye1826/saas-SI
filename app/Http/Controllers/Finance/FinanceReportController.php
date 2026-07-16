<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\FinanceReportService;
use App\Support\DashboardPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceReportController extends Controller
{
    public function __construct(private readonly FinanceReportService $reportService)
    {
    }

    public function index(Request $request): View
    {
        $period = DashboardPeriod::fromRequest($request);
        $types = FinanceReportService::TYPES;

        return view('finance.reports.index', compact('period', 'types'));
    }

    public function pdf(Request $request, string $type): Response
    {
        $this->validateType($type);
        $period = DashboardPeriod::fromRequest($request);
        $pdfContent = $this->reportService->renderPdf($type, $period);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . Str::slug(FinanceReportService::TYPES[$type]) . '.pdf"',
        ]);
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $this->validateType($type);
        $period = DashboardPeriod::fromRequest($request);

        return $this->reportService->streamCsv($type, $period);
    }

    private function validateType(string $type): void
    {
        abort_unless(array_key_exists($type, FinanceReportService::TYPES), 404);
    }
}
