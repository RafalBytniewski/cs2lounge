<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TradeOffer extends Model
{
    protected $fillable = [
        'user_id',
        'want_type',
        'want_label',
        'want_details',
        'notes',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inventoryItems(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class)->withTimestamps();
    }
}
