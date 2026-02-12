<?php

declare(strict_types=1);

namespace App\Cart;

use App\Cart\Contracts\CartItemInterface;
use App\Models\Product;

class CartItem implements CartItemInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly float $unitPrice,
        public readonly float $taxRate,
        public float $quantity = 1,
    ) {}

    public static function createFromProduct(Product $product): self
    {
        return new self((string) $product->id, $product->name, (float) $product->price, (float) $product->tax_rate);
    }
}
