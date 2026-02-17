<?php

declare(strict_types=1);

namespace App\Cart\Riesenia;

use Riesenia\Cart\CartContext;
use Riesenia\Cart\CartItemInterface;

class DiscountCartItem implements CartItemInterface
{
    private float $quantity = 1.0;

    public function __construct(
        private readonly float $discountPrice,
        private readonly string $promoCodeLabel,
    ) {}

    public function getCartId(): string
    {
        return 'promo_discount';
    }

    public function getCartType(): string
    {
        return 'discount';
    }

    public function getCartName(): string
    {
        return 'Zľava: '.$this->promoCodeLabel;
    }

    public function setCartContext(CartContext $context): void {}

    public function setCartQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getCartQuantity(): float
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->discountPrice;
    }

    public function getTaxRate(): float
    {
        return 0.0;
    }
}
