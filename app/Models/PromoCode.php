<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\PromoCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property int $discount
 * @property bool $used
 * @property Carbon $expire_at
 */
class PromoCode extends Model
{
    /** @use HasFactory<PromoCodeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'used',
        'expire_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'used' => 'boolean',
        'discount' => 'integer',
        'expire_at' => 'datetime',
    ];

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isValid(): bool
    {
        return ! $this->used && $this->expire_at->isFuture();
    }
}
