<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Avance versée par un client avant une vente, utilisable plus tard pour
 * régler une facture (voir amount_used / remaining_amount).
 */
class ClientAdvance extends Model
{
    protected $fillable = [
        'customer_id', 'financial_account_id', 'amount', 'amount_used',
        'date', 'payment_method', 'reference', 'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_used' => 'decimal:2',
        'date' => 'date',
        'payment_method' => PaymentMethod::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, (float) $this->amount - (float) $this->amount_used),
        );
    }

    public function scopeAvailable($query)
    {
        return $query->whereColumn('amount_used', '<', 'amount');
    }
}
