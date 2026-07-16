<?php

namespace App\Models;

use App\Enums\FinancialCategory;
use App\Enums\FinancialTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Ligne du grand livre de trésorerie (entrée ou sortie). Suppression douce :
 * une écriture financière ne disparaît jamais vraiment, elle est marquée
 * annulée (voir "Audit financier").
 */
class FinancialTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'financial_account_id', 'type', 'category', 'amount', 'date',
        'reference', 'description', 'supplier_id', 'customer_id',
        'related_type', 'related_id', 'attachment_path', 'is_auto_generated',
        'user_id', 'updated_by',
    ];

    protected $casts = [
        'type' => FinancialTransactionType::class,
        'category' => FinancialCategory::class,
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_auto_generated' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Vente, avance, virement... qui a généré cette écriture automatiquement. */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeIn($query)
    {
        return $query->where('type', FinancialTransactionType::In);
    }

    public function scopeOut($query)
    {
        return $query->where('type', FinancialTransactionType::Out);
    }

    public function scopeForAccount($query, int $accountId)
    {
        return $query->where('financial_account_id', $accountId);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
