<?php

declare(strict_types=1);

namespace App\Cart;

use Riesenia\Cart\Cart;
use Riesenia\Cart\CartItemInterface;

class CartService {

    public function addItem(CartItemInterface $item, int $quantity = 1): void {
        $cart = $this->loadCart();

        $cart->addItem($item, $quantity);

        $this->saveCart($cart);
    }

    private function saveCart(Cart $cart): void {
        session(['cart' => serialize($cart)]);
    }

    public function loadCart(): Cart {
        $data = session('cart');

        return $data ? unserialize($data) : new Cart();
    }

    public function toArray(): array {
        $cart = $this->loadCart();

        return [
            'items' => array_map(fn(CartItemInterface $item) => [
                'id' => $item->getCartId(),
                'name' => $item->getCartName(),
                'quantity' => $item->getCartQuantity(),
                'unitPrice' => $item->getUnitPrice(),
                'taxRate' => $item->getTaxRate(),
            ], $cart->getItems()),
            'total' => (string) $cart->getTotal(),
            'itemCount' => $cart->countItems(),
        ];
    }
}
