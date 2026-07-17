<?php

namespace App\Services;

use App\Enums\TreasuryTransactionType;
use App\Models\Payment;
use App\Models\TreasuryTransaction;
use App\Support\DashboardPeriod;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Trésorerie simplifiée pour une petite boutique : une seule caisse,
 * pas de comptes multiples ni de virements. Le solde est toujours
 * recalculé par somme (SUM entrées - SUM sorties), jamais stocké.
 */
class TreasuryService
{
    public function createExpense(array $data, int $userId): TreasuryTransaction
    {
        return TreasuryTransaction::create([
            'type' => TreasuryTransactionType::Out->value,
            'category' => $data['category'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'date' => $data['date'],
            'user_id' => $userId,
        ]);
    }

    /**
     * Écriture d'entrée générée automatiquement par une vente ou un
     * paiement complémentaire de facture (voir PaymentService::store()).
     */
    public function createAutoEntry(array $data, int $userId): TreasuryTransaction
    {
        return TreasuryTransaction::create($data + [
            'type' => TreasuryTransactionType::In->value,
            'user_id' => $userId,
        ]);
    }

    public function reverseAutoEntry(Payment $payment): void
    {
        TreasuryTransaction::where('payment_id', $payment->id)->delete();
    }

    public function paginateHistory(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return TreasuryTransaction::query()
            ->with('user')
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['start'] ?? null, fn ($q, $start) => $q->whereDate('date', '>=', $start))
            ->when($filters['end'] ?? null, fn ($q, $end) => $q->whereDate('date', '<=', $end))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getDashboard(DashboardPeriod $period): array
    {
        $mois = DashboardPeriod::month();
        $entreesPeriode = $this->sum(TreasuryTransactionType::In, $period->start, $period->end);
        $depensesPeriode = $this->sum(TreasuryTransactionType::Out, $period->start, $period->end);
        $entreesMois = $this->sum(TreasuryTransactionType::In, $mois->start, $mois->end);
        $depensesMois = $this->sum(TreasuryTransactionType::Out, $mois->start, $mois->end);

        return [
            'solde_actuel' => $this->sum(null, null, null),
            'entrees_periode' => $entreesPeriode,
            'depenses_periode' => $depensesPeriode,
            'solde_periode' => $entreesPeriode - $depensesPeriode,
            'entrees_mois' => $entreesMois,
            'depenses_mois' => $depensesMois,
            'solde_mois' => $entreesMois - $depensesMois,
        ];
    }

    public function getReport(DashboardPeriod $period): array
    {
        $entrees = $this->sum(TreasuryTransactionType::In, $period->start, $period->end);
        $depenses = $this->sum(TreasuryTransactionType::Out, $period->start, $period->end);

        return [
            'entrees' => $entrees,
            'depenses' => $depenses,
            'solde' => $entrees - $depenses,
            'transactions' => TreasuryTransaction::query()
                ->with('user')
                ->whereBetween('date', [$period->start, $period->end])
                ->orderBy('date')
                ->orderBy('id')
                ->get(),
        ];
    }

    /**
     * Total des dépenses sur une période — utilisé par le tableau de bord
     * principal (carte KPI "Dépenses"), sans le reste du payload de
     * getDashboard()/getReport().
     */
    public function getExpensesTotal(DashboardPeriod $period): float
    {
        return $this->sum(TreasuryTransactionType::Out, $period->start, $period->end);
    }

    public function renderReportPdf(DashboardPeriod $period): string
    {
        $report = $this->getReport($period);
        $entreprise = \App\Models\Entreprise::current();

        $pdf = PDF::loadView('documents.treasury_report', compact('report', 'period', 'entreprise'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->output();
    }

    private function sum(?TreasuryTransactionType $type, ?Carbon $start, ?Carbon $end): float
    {
        $inSum = fn () => (float) TreasuryTransaction::query()->in()
            ->when($start && $end, fn ($q) => $q->whereBetween('date', [$start, $end]))
            ->sum('amount');

        $outSum = fn () => (float) TreasuryTransaction::query()->out()
            ->when($start && $end, fn ($q) => $q->whereBetween('date', [$start, $end]))
            ->sum('amount');

        return match ($type) {
            TreasuryTransactionType::In => $inSum(),
            TreasuryTransactionType::Out => $outSum(),
            null => $inSum() - $outSum(),
        };
    }
}
