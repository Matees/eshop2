<?php

declare(strict_types=1);

namespace App\Cart;

use App\Cart\Adapters\RieseniaCartItemAdapter;
use App\Cart\Contracts\CartInterface;
use App\Cart\Contracts\CartItemInterface;
use Riesenia\Cart\Cart;

class CartRiesenieService implements CartInterface
{
    private ?Cart $cart = null;

    private function saveCart(Cart $cart): void
    {
        session(['cart' => serialize($cart)]);
    }

    public function getCart(): Cart
    {
        if ($this->cart !== null) {
            return $this->cart;
        }

        $data = session('cart');
        if (is_string($data) && ($unserialized = unserialize($data)) instanceof Cart) {
            $this->cart = $unserialized;

            return $this->cart;
        }

        $this->cart = new Cart;

        return $this->cart;
    }

    public function clearCart(): void
    {
        $this->cart = null;
        session()->forget('cart');
    }

    public function add(CartItemInterface $item, int $quantity = 1): CartItemInterface
    {
        $cart = $this->getCart();

        $cart->addItem(new RieseniaCartItemAdapter($item), $quantity);

        $this->saveCart($cart);

        return $item;
    }

    public function remove(string $itemId): bool
    {
        $cart = $this->getCart();

        try {
            $cart->removeItem($itemId);
        } catch (\OutOfBoundsException $e) {
            return false;
        }

        $this->saveCart($cart);

        return true;
    }

    public function getTotal(): float
    {
        return $this->getCart()->getTotal()->asFloat();
    }

    public function getSubtotal(): float
    {
        return $this->getCart()->getSubtotal()->asFloat();
    }

    public function getItems(): array
    {
        return array_map(function ($rieseniaItem) {
            return new CartItem(
                $rieseniaItem->getCartId(),
                $rieseniaItem->getCartName(),
                $rieseniaItem->getUnitPrice(),
                $rieseniaItem->getTaxRate(),
                $rieseniaItem->getCartQuantity(),
            );
        }, $this->getCart()->getItems());
    }

    public function getItemPrice(CartItemInterface $item, bool $withVat = true): float
    {
        return $this->getCart()->getItemPrice(new RieseniaCartItemAdapter($item), pricesWithVat: $withVat)->asFloat();
    }
}
