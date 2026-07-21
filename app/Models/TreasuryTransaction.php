<?php

namespace App\Models;

use App\Enums\TreasuryExpenseCategory;
use App\Enums\TreasuryTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne du grand livre de trésorerie simplifié : une seule caisse globale,
 * pas de comptes multiples. Le solde n'est jamais stocké, toujours calculé
 * par SUM(entrées) - SUM(sorties) — volume trop faible pour une petite
 * boutique pour justifier une colonne de solde entretenue en continu.
 */
class TreasuryTransaction extends Model
{
    protected $fillable = [
        'type', 'category', 'amount', 'description', 'date', 'reference',
        'supplier_name', 'product_reference', 'payment_id', 'user_id',
    ];

    protected $casts = [
        'type' => TreasuryTransactionType::class,
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeIn($query)
    {
        return $query->where('type', TreasuryTransactionType::In);
    }

    public function scopeOut($query)
    {
        return $query->where('type', TreasuryTransactionType::Out);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Les catégories des entrées auto-générées (vente, paiement...) ne font
     * pas partie de TreasuryExpenseCategory (réservée aux dépenses) : on
     * fournit ici un libellé unique pour les deux familles de catégories.
     */
    public function categoryLabel(): string
    {
        return TreasuryExpenseCategory::tryFrom((string) $this->category)?->label() ?? match ($this->category) {
            'vente' => 'Vente',
            'echange' => 'Échange',
            'paiement_facture' => 'Paiement facture',
            'paiement_complementaire' => 'Paiement complémentaire',
            default => ucfirst(str_replace('_', ' ', (string) $this->category)),
        };
    }
}
