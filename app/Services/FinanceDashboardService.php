<?php

namespace App\Services;

use App\Enums\FinancialTransactionType;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Support\DashboardPeriod;
use Carbon\Carbon;

/**
 * KPI et graphiques du tableau de bord financier. Les indicateurs "jour",
 * "mois" et "annuel" restent toujours ancrés sur la date du jour réelle
 * (repères fixes) ; les indicateurs "période" et les graphiques suivent le
 * filtre de dates sélectionné (comme le dashboard principal).
 */
class FinanceDashboardService
{
    public function getKpis(DashboardPeriod $period): array
    {
        $accounts = FinancialAccount::query()->active()->orderBy('name')->get();

        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $yearStart = $today->copy()->startOfYear();
        $yearEnd = $today->copy()->endOfYear();

        return [
            'accounts' => $accounts,
            'global_balance' => (float) $accounts->sum('current_balance'),

            'period_entries' => $this->sum(FinancialTransactionType::In, $period->start, $period->end),
            'period_exits' => $this->sum(FinancialTransactionType::Out, $period->start, $period->end),

            'today_entries' => $this->sum(FinancialTransactionType::In, $today->copy()->startOfDay(), $today->copy()->endOfDay()),
            'today_exits' => $this->sum(FinancialTransactionType::Out, $today->copy()->startOfDay(), $today->copy()->endOfDay()),

            'month_entries' => $this->sum(FinancialTransactionType::In, $monthStart, $monthEnd),
            'month_exits' => $this->sum(FinancialTransactionType::Out, $monthStart, $monthEnd),

            'year_entries' => $this->sum(FinancialTransactionType::In, $yearStart, $yearEnd),
            'year_exits' => $this->sum(FinancialTransactionType::Out, $yearStart, $yearEnd),
        ];
    }

    public function getCharts(DashboardPeriod $period): array
    {
        $days = collect();
        $cursor = $period->start->copy()->startOfDay();
        $end = $period->end->copy()->startOfDay();

        // Borne le nombre de points à 92 jours (≈ un trimestre) pour garder
        // des graphiques lisibles même sur "Cette année" ou une période
        // personnalisée très large.
        $step = max(1, (int) ceil($cursor->diffInDays($end) / 92));

        while ($cursor->lte($end)) {
            $days->push($cursor->copy());
            $cursor->addDays($step);
        }

        $revenueEvolution = $days->map(fn (Carbon $day) => $this->sum(FinancialTransactionType::In, $day, $day->copy()->addDays($step - 1)->endOfDay()));
        $expenseEvolution = $days->map(fn (Carbon $day) => $this->sum(FinancialTransactionType::Out, $day, $day->copy()->addDays($step - 1)->endOfDay()));

        return [
            'labels' => $days->map(fn (Carbon $day) => $day->translatedFormat('d M'))->all(),
            'revenue_evolution' => $revenueEvolution->all(),
            'expense_evolution' => $expenseEvolution->all(),
            'treasury_evolution' => $revenueEvolution->map(fn ($rev, $i) => $rev - $expenseEvolution[$i])->all(),
            'expense_by_category' => $this->byCategory(FinancialTransactionType::Out, $period),
            'revenue_by_category' => $this->byCategory(FinancialTransactionType::In, $period),
        ];
    }

    private function sum(FinancialTransactionType $type, $start, $end): float
    {
        return (float) FinancialTransaction::query()
            ->where('type', $type->value)
            ->whereBetween('date', [$start, $end])
            ->sum('amount');
    }

    private function byCategory(FinancialTransactionType $type, DashboardPeriod $period): array
    {
        $rows = FinancialTransaction::query()
            ->where('type', $type->value)
            ->whereBetween('date', [$period->start, $period->end])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->map(fn ($row) => $row->category->label())->all(),
            'data' => $rows->map(fn ($row) => (float) $row->total)->all(),
        ];
    }
}
