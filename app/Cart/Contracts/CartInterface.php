<?php

declare(strict_types=1);

namespace App\Cart\Contracts;

interface CartInterface
{
    public function add(CartItemInterface $item, int $quantity = 1): CartItemInterface;

    public function remove(string $itemId): bool;

    public function getTotal(): float;

    public function getSubtotal(): float;

    /**
     * @return array<CartItemInterface>
     */
    public function getItems(): array;

    public function clearCart(): void;

    public function getItemPrice(CartItemInterface $item, $withVat = true): float;
}
