<?php

namespace App\Models;

use App\Casts\AddressCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
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
        return $this->belongsToMany(Product::class)->using(OrderItems::class);
    }
}
