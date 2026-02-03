<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $image
 * @property-read string $description
 * @property-read string $price
 * @property-read string $tax_rate
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /** @return BelongsToMany<Order, $this, OrderItems, 'pivot'> */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->using(OrderItems::class);
    }
}
