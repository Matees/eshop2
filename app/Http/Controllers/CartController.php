<?php

namespace App\Http\Controllers;

use App\Cart\CartItem;
use App\Cart\Contracts\CartInterface;
use App\Enums\FlashType;
use App\Http\Requests\StoreCartItemRequest;
use App\Models\Product;
use App\Models\PromoCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Cart/Index');
    }

    /**
     * Add Product to cart.
     */
    public function add(int $itemId, StoreCartItemRequest $request, CartInterface $cart): RedirectResponse
    {
        $product = Product::query()->findOrFail($itemId);
        $quantity = $request->integer('quantity', 1);

        $cart->add(CartItem::createFromProduct($product), $quantity);

        return redirect()->back()->with(FlashType::Success->value, 'Položka pridaná');
    }

    /**
     * Remove Product from cart.
     */
    public function remove(int $itemId, CartInterface $cart): RedirectResponse
    {
        $removed = $cart->remove((string) $itemId);

        if ($removed) {
            return redirect()->back()->with(FlashType::Success->value, 'Položka zmazaná');
        }

        return redirect()->back()->with(FlashType::Warning->value, 'Položka už nie je v košíku');
    }

    public function verifyPromoCode(Request $request, CartInterface $cart): JsonResponse
    {
        $code = $request->string('promo_code');

        $promoCode = PromoCode::query()->where('code', $code)->first();

        if (! $promoCode || ! $promoCode->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Promo kód je neplatný alebo už bol použitý.',
            ], 422);
        }

        $cart->applyPromoCode($promoCode);

        return response()->json([
            'valid' => true,
            'code' => $promoCode->code,
            'discount' => $promoCode->discount,
            'discount_amount' => $cart->getDiscountAmount(),
            'total' => $cart->getTotal(),
        ]);
    }
}
