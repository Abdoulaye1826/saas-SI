<?php

namespace App\Models;

use App\Enums\FinancialAccountType;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Compte financier (Caisse, Banque, Wave, Orange Money...). Le solde est
 * une colonne stockée, mise à jour à chaque écriture par
 * FinancialTransactionService — jamais recalculée à la volée.
 */
class FinancialAccount extends Model
{
    protected $fillable = [
        'name', 'type', 'payment_method', 'current_balance', 'is_active', 'description',
    ];

    protected $casts = [
        'type' => FinancialAccountType::class,
        'payment_method' => PaymentMethod::class,
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Le compte associé à un mode de paiement (Wave, Orange Money, Espèces). */
    public static function forPaymentMethod(PaymentMethod $method): ?self
    {
        return self::query()->active()->where('payment_method', $method->value)->first();
    }
}
