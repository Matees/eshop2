<?php

declare(strict_types=1);

namespace App\Actions;

use App\Cart\Contracts\CartInterface;
use App\Http\Requests\StoreOrderRequest;
use App\Mail\OrderCreated;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

readonly class CreateOrderAction
{
    public function __construct(private CartInterface $cart) {}

    /**
     * @param  array{email: string, phone?: string, street: string, houseNumber: string, city: string, zip: string, promo_code: string}  $validated
     */
    public function execute(array $validated, StoreOrderRequest $storeOrderRequest): Order
    {
        $promoCode = $storeOrderRequest->getPromoCode();

        $this->cart->applyPromoCode($promoCode);

        $order = Order::query()->create([
            'user_id' => Auth::id(),
            'promo_code_id' => $promoCode?->id,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => new Address(
                lineOne: $validated['street'].', '.$validated['houseNumber'],
                lineTwo: $validated['city'],
                lineThree: $validated['zip'],
            ),
            'total' => $this->cart->getTotal(),
            'subtotal' => $this->cart->getSubtotal(),
            'discount_amount' => $this->cart->getDiscountAmount(),
        ]);

        $items = [];
        foreach ($this->cart->getItems() as $item) {
            $items[$item->id] = [
                'unit_price' => $item->unitPrice,
                'quantity' => $item->quantity,
                'tax_rate' => $item->taxRate,
                'total' => $item->totalPrice,
            ];
        }
        $order->products()->attach($items);

        $promoCode?->update(['used' => true]);

        $this->cart->clearCart();

        Mail::to($order->email)->queue(new OrderCreated($order));

        return $order;
    }
}
