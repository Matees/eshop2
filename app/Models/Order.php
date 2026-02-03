<?php

namespace App\Models;

use App\Casts\AddressCast;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $email
 * @property string $status
 * @property string $phone
 * @property string $address_line_one
 * @property string $address_line_two
 * @property string $address_line_three
 * @property float $total
 */
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'address',
        'total',
        'subtotal',
    ];

    protected $casts = [
        'address' => AddressCast::class,
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->using(OrderItems::class)
            ->withPivot(['unit_price', 'quantity', 'tax_rate', 'total'])
            ->withTimestamps();
    }
}
