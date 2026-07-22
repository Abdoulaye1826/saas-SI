<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\QuoteStatus;
use App\Enums\SaleStatus;
use App\Enums\SaleType;
use App\Enums\WarrantyDuration;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Support\DashboardPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Agrège les statistiques affichées sur le tableau de bord.
 *
 * Plusieurs méthodes acceptent un `DashboardPeriod` optionnel : quand il est
 * omis (`null`), le comportement historique (utilisé par la page Rapports)
 * est préservé à l'identique. Quand il est fourni (tableau de bord avec
 * filtre de période), les résultats sont bornés à cette période.
 */
class DashboardService
{
    public function __construct(private readonly TreasuryService $treasuryService)
    {
    }

    public function getStats(?DashboardPeriod $period = null): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Ventes qui comptent réellement dans le CA : validées ET dont la
        // facture n'a pas été annulée après coup (sinon la vente reste
        // "validated" mais ne doit plus alimenter les chiffres financiers).
        $validatedSales = Sale::revenueEligible();

        $salesCountMonth = (clone $validatedSales)->where('sale_date', '>=', $startOfMonth)->count();
        $revenueMonth = (float) (clone $validatedSales)->where('sale_date', '>=', $startOfMonth)->sum('total_ttc');

        $marginMonth = (float) SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('invoices', 'invoices.sale_id', '=', 'sales.id')
            ->where('sales.status', SaleStatus::Validated->value)
            ->where('sales.sale_type', SaleType::Vente->value)
            ->where('sales.sale_date', '>=', $startOfMonth)
            ->where(fn ($q) => $q->whereNull('invoices.status')->orWhere('invoices.status', '!=', InvoiceStatus::Cancelled->value))
            ->sum(DB::raw('(sale_items.unit_price - products.purchase_price) * sale_items.quantity'));

        // ── Paiements (toutes factures non annulées) ──
        $invoicePaymentRows = Invoice::query()
            ->where('status', '!=', InvoiceStatus::Cancelled->value)
            ->select('total_ttc', DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.id) as paid'))
            ->get();
        $amountPaidTotal = (float) $invoicePaymentRows->sum('paid');
        $remainingAmountTotal = (float) $invoicePaymentRows->sum(fn ($row) => max(0, (float) $row->total_ttc - (float) $row->paid));

        // ── Devis ──
        $quotesCount = Quote::count();
        $quotesConvertedCount = Quote::where('status', QuoteStatus::Converted)->count();

        $stats = [
            'revenue_today' => (float) (clone $validatedSales)->forDate($today)->sum('total_ttc'),
            'revenue_month' => $revenueMonth,
            'sales_count' => Sale::revenueEligible()->count(),
            'invoices_count' => Invoice::count(),
            'paid_invoices_count' => Invoice::where('status', InvoiceStatus::Paid)->count(),
            'pending_invoices_count' => Invoice::where('status', InvoiceStatus::Issued)->count(),
            'amount_paid_total' => $amountPaidTotal,
            'remaining_amount_total' => $remainingAmountTotal,
            'quotes_count' => $quotesCount,
            'quotes_pending_count' => Quote::whereIn('status', [QuoteStatus::Draft, QuoteStatus::Sent])->count(),
            'quotes_accepted_count' => Quote::where('status', QuoteStatus::Accepted)->count(),
            'quotes_converted_count' => $quotesConvertedCount,
            'quotes_conversion_rate' => $quotesCount > 0 ? round(($quotesConvertedCount / $quotesCount) * 100) : 0,
            'products_count' => Product::count(),
            'low_stock_count' => Product::lowStock()->count(),
            'out_of_stock_count' => Product::outOfStock()->count(),
            'customers_count' => Customer::count(),
            'new_customers_month' => Customer::where('registered_at', '>=', $startOfMonth)->count(),
            'new_customers_today' => Customer::whereDate('registered_at', $today)->count(),

            // ── Statistiques additionnelles ──
            'stock_value' => (float) Product::query()->sum(DB::raw('stock_quantity * purchase_price')),
            'average_sale_amount' => $salesCountMonth > 0 ? round($revenueMonth / $salesCountMonth, 2) : 0.0,
            'exchanges_count_month' => (clone $validatedSales)
                ->where('sale_type', SaleType::Echange)
                ->where('sale_date', '>=', $startOfMonth)
                ->count(),
            'margin_month' => $marginMonth,
        ];

        if ($period !== null) {
            $stats['period'] = $this->getStatsForPeriod($period);
        }

        return $stats;
    }

    /**
     * Statistiques bornées à la période sélectionnée sur le filtre du
     * tableau de bord (voir DashboardPeriod). Séparé de getStats() pour ne
     * jamais modifier le comportement/les clés historiques consommées par
     * la page Rapports.
     */
    private function getStatsForPeriod(DashboardPeriod $period): array
    {
        $start = $period->start;
        $end = $period->end;

        $periodSales = Sale::revenueEligible()->whereBetween('sale_date', [$start, $end]);
        $salesCount = (clone $periodSales)->count();
        $revenue = (float) (clone $periodSales)->sum('total_ttc');

        $productsSoldQty = (int) SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('invoices', 'invoices.sale_id', '=', 'sales.id')
            ->where('sales.status', SaleStatus::Validated->value)
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where(fn ($q) => $q->whereNull('invoices.status')->orWhere('invoices.status', '!=', InvoiceStatus::Cancelled->value))
            ->sum('sale_items.quantity');

        $margin = (float) SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('invoices', 'invoices.sale_id', '=', 'sales.id')
            ->where('sales.status', SaleStatus::Validated->value)
            ->where('sales.sale_type', SaleType::Vente->value)
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where(fn ($q) => $q->whereNull('invoices.status')->orWhere('invoices.status', '!=', InvoiceStatus::Cancelled->value))
            ->sum(DB::raw('(sale_items.unit_price - products.purchase_price) * sale_items.quantity'));

        $invoiceRows = Invoice::query()
            ->where('status', '!=', InvoiceStatus::Cancelled->value)
            ->whereBetween('issued_at', [$start, $end])
            ->select('total_ttc', DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.id) as paid'))
            ->get();
        $amountPaid = (float) $invoiceRows->sum('paid');
        $remainingAmount = (float) $invoiceRows->sum(fn ($row) => max(0, (float) $row->total_ttc - (float) $row->paid));

        $invoicesCount = Invoice::whereBetween('issued_at', [$start, $end])
            ->where('status', '!=', InvoiceStatus::Cancelled->value)
            ->count();
        $paidInvoicesCount = Invoice::whereBetween('issued_at', [$start, $end])->where('status', InvoiceStatus::Paid)->count();
        $pendingInvoicesCount = Invoice::whereBetween('issued_at', [$start, $end])->where('status', InvoiceStatus::Issued)->count();

        $customersCount = (clone $periodSales)->distinct()->count('customer_id');
        $newCustomers = Customer::whereBetween('registered_at', [$start, $end])->count();

        $echangeSales = (clone $periodSales)->where('sale_type', SaleType::Echange)->get(['id', 'exchange_details']);
        $exchangesCount = $echangeSales->count();
        $exchangesAddedAmount = (float) $echangeSales->sum(fn ($sale) => (float) ($sale->exchange_details['added_amount'] ?? 0));

        $warrantySales = (clone $periodSales)
            ->where('warranty_duration', '!=', WarrantyDuration::None->value)
            ->whereNotNull('warranty_end_date')
            ->get(['id', 'warranty_duration', 'warranty_end_date']);
        $warrantiesActive = $warrantySales->filter(fn ($sale) => $sale->warrantyStatus() === 'active')->count();
        $warrantiesExpired = $warrantySales->filter(fn ($sale) => $sale->warrantyStatus() === 'expired')->count();

        $depenses = $this->treasuryService->getExpensesTotal($period);

        return [
            'revenue' => $revenue,
            'sales_count' => $salesCount,
            'products_sold_qty' => $productsSoldQty,
            'amount_paid' => $amountPaid,
            'remaining_amount' => $remainingAmount,
            'depenses' => $depenses,
            'solde_net' => $amountPaid - $depenses,
            'invoices_count' => $invoicesCount,
            'paid_invoices_count' => $paidInvoicesCount,
            'pending_invoices_count' => $pendingInvoicesCount,
            'customers_count' => $customersCount,
            'new_customers' => $newCustomers,
            'exchanges_count' => $exchangesCount,
            'exchanges_added_amount' => $exchangesAddedAmount,
            'warranties_active_count' => $warrantiesActive,
            'warranties_expired_count' => $warrantiesExpired,
            'average_sale' => $salesCount > 0 ? round($revenue / $salesCount, 2) : 0.0,
            'margin' => $margin,
            'margin_rate' => $revenue > 0 ? round(($margin / $revenue) * 100, 1) : 0.0,
        ];
    }

    /**
     * Évolution des ventes sur la période sélectionnée, avec une granularité
     * qui s'adapte à l'étendue de la période : heure (≤ 1 jour), jour
     * (≤ ~62 jours) ou mois (au-delà). Alimente à la fois le graphique
     * "Évolution des ventes" (nombre) et "Évolution du chiffre d'affaires"
     * (montant) du tableau de bord.
     */
    public function getSalesEvolution(DashboardPeriod $period): array
    {
        $start = $period->start;
        // Ne jamais dépasser aujourd'hui : pour une période "Ce mois", la fin
        // de période est le dernier jour du mois (souvent dans le futur), ce
        // qui décalait le graphique vers des jours à venir sans donnée au
        // lieu de s'arrêter sur la date du jour.
        $end = $period->end->min(now());
        $spanDays = $start->diffInDays($end);

        if ($spanDays <= 1) {
            return $this->salesEvolutionHourly($start, $end);
        }

        if ($spanDays <= 62) {
            // Toujours au moins 15 jours affichés, même en tout début de
            // mois (ex. le 4) : on remonte avant le début de la période si
            // besoin, jusqu'à aujourd'hui inclus.
            if ($spanDays < 14) {
                $start = $end->copy()->subDays(14);
            }

            return $this->salesEvolutionDaily($start, $end);
        }

        return $this->salesEvolutionMonthly($start, $end);
    }

    private function salesEvolutionHourly(Carbon $start, Carbon $end): array
    {
        // Basé sur `sold_at` (horodatage serveur) plutôt que `sale_date`
        // (simple date) : nécessaire pour une granularité horaire. Les
        // très rares ventes antérieures à l'ajout de cette colonne
        // (`sold_at` nul) sont simplement absentes de ce graphique.
        $rows = Sale::revenueEligible()
            ->whereBetween('sold_at', [$start, $end])
            ->select(
                DB::raw('HOUR(sold_at) as hour'),
                DB::raw('SUM(total_ttc) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        $labels = [];
        $revenue = [];
        $count = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $labels[] = sprintf('%02dh', $hour);
            $row = $rows->get($hour);
            $revenue[] = $row ? (float) $row->total : 0;
            $count[] = $row ? (int) $row->count : 0;
        }

        return compact('labels', 'revenue', 'count');
    }

    private function salesEvolutionDaily(Carbon $start, Carbon $end): array
    {
        $rows = Sale::revenueEligible()
            ->whereBetween('sale_date', [$start, $end])
            ->select(
                DB::raw('DATE(sale_date) as day'),
                DB::raw('SUM(total_ttc) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $revenue = [];
        $count = [];

        $cursor = $start->copy()->startOfDay();
        $lastDay = $end->copy()->startOfDay();

        while ($cursor->lte($lastDay)) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d/m');

            $row = $rows->get($key);
            $revenue[] = $row ? (float) $row->total : 0;
            $count[] = $row ? (int) $row->count : 0;

            $cursor->addDay();
        }

        return compact('labels', 'revenue', 'count');
    }

    private function salesEvolutionMonthly(Carbon $start, Carbon $end): array
    {
        $rows = Sale::revenueEligible()
            ->whereBetween('sale_date', [$start, $end])
            ->select(
                DB::raw('YEAR(sale_date) as year'),
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_ttc) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->get();

        $labels = [];
        $revenue = [];
        $count = [];

        $cursor = $start->copy()->startOfMonth();
        $lastMonth = $end->copy()->startOfMonth();

        while ($cursor->lte($lastMonth)) {
            $labels[] = $cursor->translatedFormat('M Y');

            $row = $rows->first(fn ($r) => (int) $r->year === $cursor->year && (int) $r->month === $cursor->month);
            $revenue[] = $row ? (float) $row->total : 0;
            $count[] = $row ? (int) $row->count : 0;

            $cursor->addMonth();
        }

        return compact('labels', 'revenue', 'count');
    }

    /**
     * Ventes mensuelles des 12 derniers mois (pour la page Rapports).
     */
    public function getSalesByMonth(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();

        $rows = Sale::revenueEligible()
            ->where('sale_date', '>=', $start)
            ->select(
                DB::raw('YEAR(sale_date) as year'),
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_ttc) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths(11 - $i);
            $labels[] = $date->translatedFormat('M Y');

            $row = $rows->first(fn ($r) => (int) $r->year === $date->year && (int) $r->month === $date->month);
            $data[] = $row ? (float) $row->total : 0;
        }

        return compact('labels', 'data');
    }

    /**
     * Répartition des ventes par catégorie.
     */
    public function getSalesByCategory(?DashboardPeriod $period = null): array
    {
        $query = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('invoices', 'invoices.sale_id', '=', 'sales.id')
            ->where('sales.status', SaleStatus::Validated->value)
            ->where(fn ($q) => $q->whereNull('invoices.status')->orWhere('invoices.status', '!=', InvoiceStatus::Cancelled->value));

        if ($period !== null) {
            $query->whereBetween('sales.sale_date', [$period->start, $period->end]);
        }

        $rows = $query
            ->select('categories.name', DB::raw('SUM(sale_items.line_total) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('name')->all(),
            'data' => $rows->pluck('total')->map(fn ($v) => (float) $v)->all(),
        ];
    }

    public function getInvoiceStatusSummary(?DashboardPeriod $period = null): array
    {
        $query = Invoice::query();

        if ($period !== null) {
            $query->whereBetween('issued_at', [$period->start, $period->end]);
        }

        $rows = $query
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_ttc) as total'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $rows->map(fn ($row) => ($row->status instanceof InvoiceStatus ? $row->status : InvoiceStatus::from($row->status))->label())->all(),
            'values' => $rows->map(fn ($row) => (float) $row->total)->all(),
            'counts' => $rows->map(fn ($row) => (int) $row->count)->all(),
        ];
    }

    public function getTopCustomers(int $limit = 5, ?DashboardPeriod $period = null): array
    {
        $query = Customer::query()
            ->join('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.status', '!=', InvoiceStatus::Cancelled->value);

        if ($period !== null) {
            $query->whereBetween('invoices.issued_at', [$period->start, $period->end]);
        }

        return $query
            ->select(
                'customers.id',
                'customers.full_name',
                DB::raw('COUNT(invoices.id) as invoices_count'),
                DB::raw('SUM(invoices.total_ttc) as total_amount')
            )
            ->groupBy('customers.id', 'customers.full_name')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getSalesByUser(int $limit = 5, ?DashboardPeriod $period = null): array
    {
        $query = Sale::query()
            ->revenueEligible()
            ->join('users', 'sales.user_id', '=', 'users.id');

        if ($period !== null) {
            $query->whereBetween('sales.sale_date', [$period->start, $period->end]);
        }

        return $query
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(sales.id) as sales_count'),
                DB::raw('SUM(sales.total_ttc) as total_amount')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getRecentInvoices(int $limit = 8): array
    {
        return Invoice::query()
            ->with(['customer', 'sale'])
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getRecentQuotes(int $limit = 8): array
    {
        return Quote::query()
            ->with('customer')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * Top produits les plus vendus.
     */
    public function getTopProducts(int $limit = 5, ?DashboardPeriod $period = null): array
    {
        $query = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('invoices', 'invoices.sale_id', '=', 'sales.id')
            ->where('sales.status', SaleStatus::Validated->value)
            ->where(fn ($q) => $q->whereNull('invoices.status')->orWhere('invoices.status', '!=', InvoiceStatus::Cancelled->value));

        if ($period !== null) {
            $query->whereBetween('sales.sale_date', [$period->start, $period->end]);
        }

        return $query
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.line_total) as total_amount')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * Répartition des ventes validées entre ventes classiques et échanges.
     * Sans période fournie : 12 derniers mois glissants (comportement
     * historique, page Rapports).
     */
    public function getSalesTypeBreakdown(?DashboardPeriod $period = null): array
    {
        $query = Sale::revenueEligible();

        if ($period !== null) {
            $query->whereBetween('sale_date', [$period->start, $period->end]);
        } else {
            $query->where('sale_date', '>=', Carbon::now()->subMonths(11)->startOfMonth());
        }

        $rows = $query
            ->select('sale_type', DB::raw('COUNT(*) as count'))
            ->groupBy('sale_type')
            ->get();

        $venteCount = (int) ($rows->first(fn ($r) => $r->sale_type === SaleType::Vente)?->count ?? 0);
        $echangeCount = (int) ($rows->first(fn ($r) => $r->sale_type === SaleType::Echange)?->count ?? 0);

        return [
            'labels' => ['Ventes', 'Échanges'],
            'data' => [$venteCount, $echangeCount],
        ];
    }

    /**
     * Derniers mouvements de stock enregistrés (entrées, sorties, retours d'échange...).
     */
    public function getRecentStockMovements(int $limit = 8): array
    {
        return StockMovement::query()
            ->with('product')
            ->latest()
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * Produits en alerte stock (rupture + faible).
     */
    public function getStockAlerts(int $limit = 5): array
    {
        return Product::query()
            ->with('category')
            ->where(function ($q) {
                $q->outOfStock()
                    ->orWhere(fn ($q2) => $q2->lowStock());
            })
            ->orderBy('stock_quantity')
            ->limit($limit)
            ->get()
            ->all();
    }
}
