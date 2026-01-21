<?php

namespace App\Http\Controllers;

use App\Cart\CartItem;
use App\Cart\CartService;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function addItem(int $itemId, Request $request, CartService $cartService)
    {
        $product = Product::query()->findOrFail($itemId);
        $quantity = $request->integer('quantity', 1);

        $cartService->addItem(CartItem::createFromProduct($product), $quantity);

        return redirect()->back();
    }
}
