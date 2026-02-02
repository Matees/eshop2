<?php

namespace App\Models;

use Database\Factories\OrderItemsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItems extends Pivot
{
    /** @use HasFactory<OrderItemsFactory> */
    use HasFactory;
    protected $table = 'order_items';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
