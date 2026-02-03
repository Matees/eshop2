<?php

namespace App\Http\Controllers;

use App\Cart\Contracts\CartInterface;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        //
    }

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
            $items[$item->getId()] = [
                'unit_price' => $item->getUnitPrice(),
                'quantity' => $item->getQuantity(),
                'tax_rate' => $item->getTaxRate(),
                'total' => $cart->getItemPrice($item),
            ];
        }
        $order->products()->attach($items);

        $cart->clearCart();

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): void
    {
        //
    }
}
