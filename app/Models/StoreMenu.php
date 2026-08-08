<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreMenu extends Model
{
    protected $fillable = [
        'label',
        'url',
        'sort_order',
        'is_active',
        'opens_new_tab',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'opens_new_tab' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn ($query) => $query->orderBy('sort_order'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
