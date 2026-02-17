<?php

declare(strict_types=1);

namespace App\Cart\Contracts;

use App\Models\PromoCode;

interface CartInterface
{
    public function add(CartItemInterface $item, int $quantity = 1): CartItemInterface;

    public function remove(string $itemId): bool;

    public function getTotal(): float;

    public function getSubtotal(): float;

    public function applyPromoCode(?PromoCode $promoCode): void;

    public function getDiscountAmount(): float;

    /**
     * @return CartItemInterface[]
     */
    public function getItems(): array;

    public function clearCart(): void;
}
