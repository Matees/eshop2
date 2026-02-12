<?php

declare(strict_types=1);

namespace App\Cart\DTO;

use App\Cart\Contracts\CartItemInterface;

readonly class CartDataItem
{
    public function __construct(
        public string $id,
        public string $name,
        public float $quantity,
        public float $unitPrice,
        public float $taxRate,
    ) {}

    public static function fromCartItem(CartItemInterface $item): self
    {
        return new self(
            $item->id,
            $item->name,
            $item->quantity,
            $item->unitPrice,
            $item->taxRate,
        );
    }
}
