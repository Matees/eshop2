<?php

declare(strict_types=1);

namespace App\Actions;

use App\Cart\Contracts\CartInterface;
use App\Mail\OrderCreated;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateOrderAction
{
    /**
     * @param  array{email: string, phone?: string, street: string, houseNumber: string, city: string, zip: string}  $validated
     */
    public function execute(array $validated, CartInterface $cart): Order
    {
        $order = Order::query()->create([
            'user_id' => Auth::id(),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => new Address(
                lineOne: $validated['street'].', '.$validated['houseNumber'],
                lineTwo: $validated['city'],
                lineThree: $validated['zip'],
            ),
            'total' => $cart->getTotal(),
            'subtotal' => $cart->getSubtotal(),
        ]);

        $items = [];
        foreach ($cart->getItems() as $item) {
            $items[$item->id] = [
                'unit_price' => $item->unitPrice,
                'quantity' => $item->quantity,
                'tax_rate' => $item->taxRate,
                'total' => $item->totalPrice,
            ];
        }
        $order->products()->attach($items);

        $cart->clearCart();

        Mail::to($order->email)->queue(new OrderCreated($order));

        return $order;
    }
}
