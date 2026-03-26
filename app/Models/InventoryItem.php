<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'weapon_type',
        'exterior',
        'rarity',
        'estimated_value',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tradeOffers(): BelongsToMany
    {
        return $this->belongsToMany(TradeOffer::class)->withTimestamps();
    }
}
