<?php

declare(strict_types=1);

namespace App\Cart\Adapters;

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
        return $this->item->getId();
    }

    public function getCartType(): string
    {
        return 'product';
    }

    public function getCartName(): string
    {
        return $this->item->getName();
    }

    public function setCartContext(CartContext $context): void
    {
        // TODO: Implement setCartContext() method.
    }

    public function setCartQuantity(float $quantity): void
    {
        $this->item->setQuantity($quantity);
    }

    public function getCartQuantity(): float
    {
        return $this->item->getQuantity();
    }

    public function getUnitPrice(): float
    {
        return $this->item->getUnitPrice();
    }

    public function getTaxRate(): float
    {
        return $this->item->getTaxRate();
    }
}
