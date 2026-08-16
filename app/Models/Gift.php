<?php

namespace App\Models;

use App\Enums\GiftStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Cadeau / produit offert : sortie de stock gratuite, traçable, mais
 * volontairement séparée de Sale — jamais une vente, jamais de facture,
 * jamais de paiement ni d'entrée de trésorerie. Voir app/Services/GiftService.php.
 */
class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'gift_number',
        'customer_id',
        'user_id',
        'gift_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'gift_date' => 'datetime',
        'status' => GiftStatus::class,
    ];

    // ─── Relations ───────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Utilisateur ayant offert le produit. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GiftItem::class);
    }

    /** IMEI offerts via ce cadeau. */
    public function givenImeis(): HasMany
    {
        return $this->hasMany(ProductImei::class, 'gift_id');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeGiven($query)
    {
        return $query->where('status', GiftStatus::Given);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('gift_date', $date);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('gift_date', $year)->whereMonth('gift_date', $month);
    }

    // ─── Méthodes métier ─────────────────────────────────────

    public function isGiven(): bool
    {
        return $this->status === GiftStatus::Given;
    }

    public function isCancelled(): bool
    {
        return $this->status === GiftStatus::Cancelled;
    }

    /** Valeur indicative totale du cadeau (jamais un montant à payer). */
    public function totalValue(): float
    {
        return (float) $this->items->sum(fn (GiftItem $item) => $item->unit_value * $item->quantity);
    }
}
