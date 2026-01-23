<?php

namespace App\Http\Controllers;

use App\Cart\CartItem;
use App\Cart\CartService;
use App\Enums\FlashType;
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
    public function add(int $itemId, AddItemPostRequest $request, CartService $cartService)
    {
        $product = Product::query()->findOrFail($itemId);
        $quantity = $request->integer('quantity', 1);

        $cartService->addItem(CartItem::createFromProduct($product), $quantity);

        return redirect()->back()->with(FlashType::Success->value, 'Položka pridaná');
    }

    /**
     * Remove Product from cart.
     */
    public function remove(int $itemId, CartService $cartService)
    {
        $removed = $cartService->removeItem((string) $itemId);

        if ($removed) {
            return redirect()->back()->with(FlashType::Success->value, 'Položka zmazaná');
        }

        return redirect()->back()->with(FlashType::Warning->value, 'Položka už nie je v košíku');
    }
}
