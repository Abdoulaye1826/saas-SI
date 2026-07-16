<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ClientAdvance;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\InternalTransfer;
use App\Models\Payment;
use App\Models\SupplierAdvance;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vue en lecture seule de activity_logs, filtrée aux modèles du module
 * Finance — réutilise le journal d'audit existant plutôt qu'une table
 * dédiée (voir le plan du module).
 */
class FinanceAuditController extends Controller
{
    private const FINANCE_MODELS = [
        FinancialTransaction::class,
        FinancialAccount::class,
        InternalTransfer::class,
        ClientAdvance::class,
        SupplierAdvance::class,
        Payment::class,
    ];

    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->whereIn('model_type', self::FINANCE_MODELS)
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->input('action')))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('finance.audit.index', compact('logs'));
    }
}
