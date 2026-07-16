<?php

namespace App\Services;

use App\Enums\FinancialTransactionType;
use App\Models\ClientAdvance;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\SupplierAdvance;
use App\Support\DashboardPeriod;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Les 7 rapports financiers. Structure volontairement générique
 * (title/columns/rows) : un seul gabarit PDF et un seul générateur CSV
 * suffisent pour les 7, plutôt que 7 vues et 7 exports distincts.
 */
class FinanceReportService
{
    public const TYPES = [
        'tresorerie' => 'Rapport de trésorerie',
        'recettes' => 'Rapport des recettes',
        'depenses' => 'Rapport des dépenses',
        'benefices' => 'Rapport des bénéfices',
        'avances_clients' => 'Rapport des avances clients',
        'avances_fournisseurs' => 'Rapport des avances fournisseurs',
        'mouvements_comptes' => 'Rapport des mouvements de comptes',
    ];

    public function build(string $type, DashboardPeriod $period): array
    {
        return match ($type) {
            'tresorerie' => $this->tresorerie($period),
            'recettes' => $this->parType($period, FinancialTransactionType::In, self::TYPES['recettes']),
            'depenses' => $this->parType($period, FinancialTransactionType::Out, self::TYPES['depenses']),
            'benefices' => $this->benefices($period),
            'avances_clients' => $this->avancesClients($period),
            'avances_fournisseurs' => $this->avancesFournisseurs($period),
            'mouvements_comptes' => $this->mouvementsComptes($period),
            default => throw new \InvalidArgumentException("Type de rapport inconnu : {$type}"),
        };
    }

    private function tresorerie(DashboardPeriod $period): array
    {
        $rows = FinancialTransaction::query()
            ->with(['account'])
            ->whereBetween('date', [$period->start, $period->end])
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $balance = 0;

        return [
            'title' => self::TYPES['tresorerie'],
            'columns' => ['date' => 'Date', 'compte' => 'Compte', 'type' => 'Type', 'categorie' => 'Catégorie', 'montant' => 'Montant', 'solde_cumule' => 'Solde cumulé'],
            'rows' => $rows->map(function (FinancialTransaction $t) use (&$balance) {
                $balance += $t->type === FinancialTransactionType::In ? (float) $t->amount : -(float) $t->amount;

                return [
                    'date' => $t->date->format('d/m/Y'),
                    'compte' => $t->account->name,
                    'type' => $t->type->label(),
                    'categorie' => $t->category->label(),
                    'montant' => number_format((float) $t->amount, 0, ',', ' ') . ' FCFA',
                    'solde_cumule' => number_format($balance, 0, ',', ' ') . ' FCFA',
                ];
            })->all(),
        ];
    }

    private function parType(DashboardPeriod $period, FinancialTransactionType $type, string $title): array
    {
        $rows = FinancialTransaction::query()
            ->with(['account', 'customer', 'supplier'])
            ->where('type', $type->value)
            ->whereBetween('date', [$period->start, $period->end])
            ->orderBy('date')
            ->get();

        return [
            'title' => $title,
            'columns' => ['date' => 'Date', 'compte' => 'Compte', 'categorie' => 'Catégorie', 'tiers' => 'Client/Fournisseur', 'reference' => 'Référence', 'montant' => 'Montant'],
            'rows' => $rows->map(fn (FinancialTransaction $t) => [
                'date' => $t->date->format('d/m/Y'),
                'compte' => $t->account->name,
                'categorie' => $t->category->label(),
                'tiers' => $t->customer?->full_name ?? $t->supplier?->name ?? '—',
                'reference' => $t->reference ?? '—',
                'montant' => number_format((float) $t->amount, 0, ',', ' ') . ' FCFA',
            ])->all(),
        ];
    }

    private function benefices(DashboardPeriod $period): array
    {
        $rows = FinancialTransaction::query()
            ->whereBetween('date', [$period->start, $period->end])
            ->selectRaw('date, type, SUM(amount) as total')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy(fn ($row) => $row->date->format('Y-m-d'));

        $data = [];
        foreach ($rows as $date => $group) {
            $entrees = (float) ($group->firstWhere('type', FinancialTransactionType::In)->total ?? 0);
            $sorties = (float) ($group->firstWhere('type', FinancialTransactionType::Out)->total ?? 0);

            $data[] = [
                'date' => Carbon::parse($date)->format('d/m/Y'),
                'recettes' => number_format($entrees, 0, ',', ' ') . ' FCFA',
                'depenses' => number_format($sorties, 0, ',', ' ') . ' FCFA',
                'benefice' => number_format($entrees - $sorties, 0, ',', ' ') . ' FCFA',
            ];
        }

        return [
            'title' => self::TYPES['benefices'],
            'columns' => ['date' => 'Date', 'recettes' => 'Recettes', 'depenses' => 'Dépenses', 'benefice' => 'Bénéfice'],
            'rows' => $data,
        ];
    }

    private function avancesClients(DashboardPeriod $period): array
    {
        $rows = ClientAdvance::query()
            ->with('customer')
            ->whereBetween('date', [$period->start, $period->end])
            ->orderBy('date')
            ->get();

        return [
            'title' => self::TYPES['avances_clients'],
            'columns' => ['date' => 'Date', 'client' => 'Client', 'montant' => 'Montant', 'utilise' => 'Utilisé', 'restant' => 'Restant'],
            'rows' => $rows->map(fn (ClientAdvance $a) => [
                'date' => $a->date->format('d/m/Y'),
                'client' => $a->customer->full_name,
                'montant' => number_format((float) $a->amount, 0, ',', ' ') . ' FCFA',
                'utilise' => number_format((float) $a->amount_used, 0, ',', ' ') . ' FCFA',
                'restant' => number_format((float) $a->remaining_amount, 0, ',', ' ') . ' FCFA',
            ])->all(),
        ];
    }

    private function avancesFournisseurs(DashboardPeriod $period): array
    {
        $rows = SupplierAdvance::query()
            ->with('supplier')
            ->whereBetween('date', [$period->start, $period->end])
            ->orderBy('date')
            ->get();

        return [
            'title' => self::TYPES['avances_fournisseurs'],
            'columns' => ['date' => 'Date', 'fournisseur' => 'Fournisseur', 'montant' => 'Montant', 'utilise' => 'Utilisé', 'restant' => 'Restant'],
            'rows' => $rows->map(fn (SupplierAdvance $a) => [
                'date' => $a->date->format('d/m/Y'),
                'fournisseur' => $a->supplier->name,
                'montant' => number_format((float) $a->amount, 0, ',', ' ') . ' FCFA',
                'utilise' => number_format((float) $a->amount_used, 0, ',', ' ') . ' FCFA',
                'restant' => number_format((float) $a->remaining_amount, 0, ',', ' ') . ' FCFA',
            ])->all(),
        ];
    }

    private function mouvementsComptes(DashboardPeriod $period): array
    {
        $accounts = FinancialAccount::query()->orderBy('name')->get();

        $rows = $accounts->map(function (FinancialAccount $account) use ($period) {
            $entrees = (float) FinancialTransaction::query()->forAccount($account->id)->in()
                ->whereBetween('date', [$period->start, $period->end])->sum('amount');
            $sorties = (float) FinancialTransaction::query()->forAccount($account->id)->out()
                ->whereBetween('date', [$period->start, $period->end])->sum('amount');

            return [
                'compte' => $account->name,
                'entrees' => number_format($entrees, 0, ',', ' ') . ' FCFA',
                'sorties' => number_format($sorties, 0, ',', ' ') . ' FCFA',
                'net' => number_format($entrees - $sorties, 0, ',', ' ') . ' FCFA',
                'solde_actuel' => number_format((float) $account->current_balance, 0, ',', ' ') . ' FCFA',
            ];
        });

        return [
            'title' => self::TYPES['mouvements_comptes'],
            'columns' => ['compte' => 'Compte', 'entrees' => 'Entrées', 'sorties' => 'Sorties', 'net' => 'Net', 'solde_actuel' => 'Solde actuel'],
            'rows' => $rows->all(),
        ];
    }

    public function renderPdf(string $type, DashboardPeriod $period): string
    {
        $report = $this->build($type, $period);

        $pdf = PDF::loadView('documents.finance_report', [
            'report' => $report,
            'period' => $period,
            'entreprise' => \App\Models\Entreprise::current(),
        ])->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->output();
    }

    public function streamCsv(string $type, DashboardPeriod $period): StreamedResponse
    {
        $report = $this->build($type, $period);
        $filename = Str::slug($report['title']) . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 : Excel sous Windows interprète mal les accents sans lui.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_values($report['columns']), ';');
            foreach ($report['rows'] as $row) {
                fputcsv($handle, array_values($row), ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
