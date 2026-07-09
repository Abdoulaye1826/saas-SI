<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Devis : proposition de prix envoyée à un client avant vente, sans impact
 * sur le stock. Peut être converti en vente (brouillon) une fois accepté.
 */
class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'customer_id',
        'user_id',
        'quote_date',
        'valid_until',
        'discount_amount',
        'subtotal_ht',
        'total_ttc',
        'status',
        'notes',
        'converted_sale_id',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'discount_amount' => 'decimal:2',
        'subtotal_ht' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'status' => QuoteStatus::class,
    ];

    // ─── Relations ───────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    // ─── Méthodes métier ─────────────────────────────────────

    /**
     * Un devis n'est "expiré" que s'il est toujours en attente d'une
     * décision du client (brouillon ou envoyé) et que sa date de validité
     * est dépassée — un devis déjà accepté/refusé/converti n'expire plus.
     */
    public function isExpired(): bool
    {
        if ($this->valid_until === null) {
            return false;
        }

        if (! in_array($this->status, [QuoteStatus::Draft, QuoteStatus::Sent], true)) {
            return false;
        }

        return $this->valid_until->isPast() && ! $this->valid_until->isToday();
    }
}
