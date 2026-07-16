<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Virement entre deux comptes financiers (ex : Wave -> Caisse principale).
 * Chaque virement génère deux FinancialTransaction liées (une sortie sur
 * le compte source, une entrée sur le compte destination).
 */
class InternalTransfer extends Model
{
    protected $fillable = [
        'from_account_id', 'to_account_id', 'amount', 'date', 'reason', 'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'to_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
