<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Avance versée à un fournisseur, historisée séparément des sorties
 * de trésorerie classiques.
 */
class SupplierAdvance extends Model
{
    protected $fillable = [
        'supplier_id', 'financial_account_id', 'amount', 'amount_used',
        'date', 'reference', 'observation', 'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_used' => 'decimal:2',
        'date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, (float) $this->amount - (float) $this->amount_used),
        );
    }
}
