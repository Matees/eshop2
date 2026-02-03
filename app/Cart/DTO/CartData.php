<?php

declare(strict_types=1);

namespace App\Cart\DTO;

use App\Cart\Contracts\CartInterface;

readonly class CartData
{
    /**
     * @param  array<CartDataItem>  $items
     */
    public function __construct(
        public array $items,
        public float $total,
        public float $subtotal,
        public int $itemCount,
    ) {}

    public static function fromCart(CartInterface $cart): self
    {
        $items = [];
        foreach ($cart->getItems() as $item) {
            $items[$item->getId()] = CartDataItem::fromCartItem($item);
        }

        return new self(
            $items,
            $cart->getTotal(),
            $cart->getSubtotal(),
            count($cart->getItems()),
        );
    }
}
