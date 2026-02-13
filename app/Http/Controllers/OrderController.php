<?php

namespace App\Http\Controllers;

use App\Cart\Contracts\CartInterface;
use App\Enums\FlashType;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Orders/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $storeOrderRequest, CartInterface $cart): RedirectResponse
    {
        $validated = $storeOrderRequest->validated();

        $order = Order::query()->create([
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

        return redirect('/')->with(FlashType::Success->value, 'Objednavka bola vytvorena');
    }
}
