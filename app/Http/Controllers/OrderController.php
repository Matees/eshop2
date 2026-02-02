<?php

namespace App\Http\Controllers;

use App\Cart\CartService;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Orders/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $storeOrderRequest, CartService $cartService)
    {
        $validated = $storeOrderRequest->validated();
        $cart = $cartService->getCart();

        $order = Order::query()->create([
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => new Address(
                lineOne: $validated['street'].', '.$validated['houseNumber'],
                lineTwo: $validated['city'],
                lineThree: $validated['zip'],
            ),
            'total' => $cart->getTotal()->asFloat(),
            'subtotal' => $cart->getSubtotal()->asFloat(),
        ]);

        $items = [];
        foreach ($cart->getItems() as $item) {
            $items[$item->getCartId()] = [
                'unit_price' => $item->getUnitPrice(),
                'quantity' => $item->getCartQuantity(),
                'tax_rate' => $item->getTaxRate(),
                'total' => $cart->getItemPrice($item, $item->getCartQuantity(), true)->asFloat(),
            ];
        }
        $order->products()->attach($items);

        $cartService->clearCart();

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
