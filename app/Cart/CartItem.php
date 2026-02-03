<?php

declare(strict_types=1);

namespace App\Cart;

use App\Cart\Contracts\CartItemInterface;
use App\Models\Product;

class CartItem implements CartItemInterface
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly float $price,
        private readonly float $taxRate,
        private float $quantity = 1
    ) {}

    public static function createFromProduct(Product $product): self
    {
        return new self((string) $product->id, $product->name, (float) $product->price, (float) $product->tax_rate);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->price;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }
}
