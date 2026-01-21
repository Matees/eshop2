<?php

declare(strict_types=1);

namespace App\Cart;

use App\Models\Product;
use Riesenia\Cart\CartContext;
use Riesenia\Cart\CartItemInterface;

class CartItem implements CartItemInterface {

    private float $quantity = 1;
    public function __construct(private string $id, private string $name, private float $price, private float $taxRate)
    {
    }

    public function getCartId(): string
    {
        return $this->id;
    }

    public function getCartType(): string
    {
        return 'product';
    }

    public function getCartName(): string
    {
        return $this->name;
    }

    public function setCartContext(CartContext $context): void
    {
        // TODO: Implement setCartContext() method.
    }

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
        return (float) $this->price;
    }

    public function getTaxRate(): float
    {
        return (float) $this->taxRate;
    }

    public static function createFromProduct(Product $product): self {
        return new self((string) $product->id, $product->name, (float) $product->price, (float) $product->tax_rate);
    }
}
