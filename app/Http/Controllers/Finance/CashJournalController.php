<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Services\FinancialTransactionService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CashJournalController extends Controller
{
    public function __construct(private readonly FinancialTransactionService $transactionService)
    {
    }

    public function index(Request $request): View
    {
        [$account, $date, $data] = $this->buildJournal($request);
        $accounts = FinancialAccount::query()->active()->orderBy('name')->get();

        return view('finance.journal.index', compact('account', 'date', 'data', 'accounts'));
    }

    public function pdf(Request $request): Response
    {
        [$account, $date, $data] = $this->buildJournal($request);
        $entreprise = \App\Models\Entreprise::current();

        $pdf = PDF::loadView('documents.cash_journal', compact('account', 'date', 'data', 'entreprise'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->download("journal-caisse-{$date->format('Y-m-d')}.pdf");
    }

    private function buildJournal(Request $request): array
    {
        $date = $request->filled('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $accountId = $request->integer('financial_account_id') ?: FinancialAccount::query()->active()->value('id');
        $account = FinancialAccount::findOrFail($accountId);

        $opening = $this->transactionService->openingBalance($account, $date);

        $transactions = FinancialTransaction::query()
            ->with(['customer', 'supplier'])
            ->forAccount($account->id)
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $entries = (float) $transactions->where('type', \App\Enums\FinancialTransactionType::In)->sum('amount');
        $exits = (float) $transactions->where('type', \App\Enums\FinancialTransactionType::Out)->sum('amount');

        return [$account, $date, [
            'opening' => $opening,
            'entries' => $entries,
            'exits' => $exits,
            'closing' => $opening + $entries - $exits,
            'transactions' => $transactions,
        ]];
    }
}
