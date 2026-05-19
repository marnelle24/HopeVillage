<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketplace_order_id',
        'marketplace_item_id',
        'quantity',
        'points_per_item',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'points_per_item' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function marketplaceItem(): BelongsTo
    {
        return $this->belongsTo(MarketplaceItem::class, 'marketplace_item_id');
    }

    public function linePointsTotal(): int
    {
        return $this->points_per_item * $this->quantity;
    }
}
