<?php

declare(strict_types=1);

namespace App\Cart;

use Riesenia\Cart\Cart;
use Riesenia\Cart\CartItemInterface;

class CartService
{
    private ?Cart $cart = null;

    public function addItem(CartItemInterface $item, int $quantity = 1): void
    {
        $cart = $this->getCart();

        $cart->addItem($item, $quantity);

        $this->saveCart($cart);
    }

    public function removeItem(string $itemId): bool
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

    private function saveCart(Cart $cart): void
    {
        session(['cart' => serialize($cart)]);
    }

    public function getCart(): Cart
    {
        if ($this->cart === null) {
            $data = session('cart');
            $this->cart = $data ? unserialize($data) : new Cart;
        }

        return $this->cart;
    }

    public function clearCart(): void
    {
        $this->getCart()->clear();
        session()->forget('cart');
    }

    public function toArray(): array
    {
        $cart = $this->getCart();

        return [
            'items' => array_map(fn (CartItemInterface $item) => [
                'id' => $item->getCartId(),
                'name' => $item->getCartName(),
                'quantity' => $item->getCartQuantity(),
                'unitPrice' => $item->getUnitPrice(),
                'taxRate' => $item->getTaxRate(),
                'total' => $cart->getItemPrice($item, $item->getCartQuantity(), true)->asFloat(),
                'subTotal' => $cart->getItemPrice($item, $item->getCartQuantity(), false)->asFloat(),
            ], $cart->getItems()),
            'total' => $cart->getTotal()->asFloat(),
            'subTotal' => $cart->getSubtotal()->asFloat(),
            'itemCount' => $cart->countItems(),
        ];
    }
}
