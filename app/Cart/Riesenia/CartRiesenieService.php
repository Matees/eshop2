<?php

declare(strict_types=1);

namespace App\Cart\Riesenia;

use App\Cart\CartItem;
use App\Cart\Contracts\CartInterface;
use App\Cart\Contracts\CartItemInterface;
use App\Cart\Riesenia\Adapters\RieseniaCartItemAdapter;
use Riesenia\Cart\Cart;
use Riesenia\Cart\CartItemInterface as RieseniaCartItemInterface;

class CartRiesenieService implements CartInterface
{
    private ?Cart $cart = null;

    private function saveCart(Cart $cart): void
    {
        session(['cart' => serialize($cart)]);
    }

    private function getCart(): Cart
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

    /**
     * @return CartItemInterface[]
     */
    public function getItems(): array
    {
        $cart = $this->getCart();

        return array_map(fn (RieseniaCartItemInterface $rieseniaItem) => new CartItem(
            id: $rieseniaItem->getCartId(),
            name: $rieseniaItem->getCartName(),
            unitPrice: $rieseniaItem->getUnitPrice(),
            taxRate: $rieseniaItem->getTaxRate(),
            quantity: $rieseniaItem->getCartQuantity(),
            totalPrice: $cart->getItemPrice($rieseniaItem)->asFloat(),
        ), $cart->getItems());
    }
}
