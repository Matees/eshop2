<?php

namespace App\Http\Controllers;

use App\Cart\CartItem;
use App\Cart\CartService;
use App\Http\Requests\AddItemPostRequest;
use App\Models\Product;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        return Inertia::render('Cart/Index');
    }

    /**
     * Add Product to cart.
     */
    public function addItem(int $itemId, AddItemPostRequest $request, CartService $cartService)
    {
        $product = Product::query()->findOrFail($itemId);
        $quantity = $request->integer('quantity', 1);

        $cartService->addItem(CartItem::createFromProduct($product), $quantity);

        return redirect()->back();
    }
}
