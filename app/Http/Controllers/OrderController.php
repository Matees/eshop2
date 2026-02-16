<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Cart\Contracts\CartInterface;
use App\Enums\FlashType;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Orders/Create');
    }

    public function store(StoreOrderRequest $storeOrderRequest, CartInterface $cart, CreateOrderAction $action): RedirectResponse
    {
        $action->execute($storeOrderRequest->validated(), $cart);

        return redirect(route('products.index'))->with(FlashType::Success->value, 'Objednavka bola vytvorena');
    }

    public function show(Order $order): Response
    {
        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}
