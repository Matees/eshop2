<?php

namespace App\Models;

use App\Casts\AddressCast;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
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
        'user_id',
        'promo_code_id',
        'email',
        'phone',
        'address',
        'total',
        'subtotal',
        'discount_amount',
    ];

    /** @var array<string, class-string> */
    protected $casts = [
        'address' => AddressCast::class,
    ];

    /** @return BelongsTo<PromoCode, $this> */
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    /** @return BelongsToMany<Product, $this, OrderItems, 'pivot'> */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->using(OrderItems::class)
            ->withPivot(['unit_price', 'quantity', 'tax_rate', 'total'])
            ->withTimestamps();
    }
}
