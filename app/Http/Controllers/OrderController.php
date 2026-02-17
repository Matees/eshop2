<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Enums\FlashType;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
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

    public function store(StoreOrderRequest $storeOrderRequest, CreateOrderAction $action): RedirectResponse
    {
        $action->execute($storeOrderRequest->validated(), $storeOrderRequest);

        return redirect(route('products.index'))->with(FlashType::Success->value, 'Objednavka bola vytvorena');
    }

    public function show(Order $order): Response
    {
        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}
