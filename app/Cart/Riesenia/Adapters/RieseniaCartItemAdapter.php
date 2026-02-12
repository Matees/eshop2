<?php

declare(strict_types=1);

namespace App\Cart\Riesenia\Adapters;

use App\Cart\Contracts\CartItemInterface;
use Riesenia\Cart\CartContext;
use Riesenia\Cart\CartItemInterface as RieseniaCartItemInterface;

class RieseniaCartItemAdapter implements RieseniaCartItemInterface
{
    public function __construct(
        private CartItemInterface $item,
    ) {}

    public function getCartId(): string
    {
        return $this->item->id;
    }

    public function getCartType(): string
    {
        return 'product';
    }

    public function getCartName(): string
    {
        return $this->item->name;
    }

    public function setCartContext(CartContext $context): void
    {
        // TODO: Implement setCartContext() method.
    }

    public function setCartQuantity(float $quantity): void
    {
        $this->item->quantity = $quantity;
    }

    public function getCartQuantity(): float
    {
        return $this->item->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->item->unitPrice;
    }

    public function getTaxRate(): float
    {
        return $this->item->taxRate;
    }
}
