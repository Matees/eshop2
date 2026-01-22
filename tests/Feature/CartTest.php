<?php

use App\Cart\CartService;
use App\Models\Product;

beforeEach(function () {
    $this->cartService = app(CartService::class);
});

test('can add item to cart', function (int $quantity) {
    $product = createProduct();

    $response = $this->post(route('cart.add', $product->id), [
        'quantity' => $quantity,
    ]);

    $response->assertRedirect();

    $cart = $this->cartService->toArray();

    expect($cart['itemCount'])->toBe(1)
        ->and($cart['items'][$product->id]['name'])->toBe('Test Product')
        ->and($cart['items'][$product->id]['quantity'])->toBe((float) $quantity)
        ->and($cart['items'][$product->id]['unitPrice'])->toBe($product->price);
})->with([1, 2, 5, 10]);

test('can increase count of existing item', function (int $quantity) {
    $product = createProduct();

    $this->post(route('cart.add', $product->id), [
        'quantity' => $quantity,
    ]);

    $this->post(route('cart.add', $product->id), [
        'quantity' => $quantity,
    ]);

    $cart = $this->cartService->toArray();

    expect($cart['items'][$product->id]['quantity'])->toBe((float) ($quantity * 2));
})->with([1, 2, 5, 10]);

test('cannot add non-existing product', function () {
    $response = $this->post('cart/add/1111');

    $response->assertNotFound();
});

test('can add multiple items', function (int $quantity) {
    $product = createProduct();

    $this->post(route('cart.add', $product->id), [
        'quantity' => $quantity,
    ]);

    $product2 = createProduct();

    $this->post('cart/add/'.$product2->id, [
        'quantity' => $quantity,
    ]);

    $cart = $this->cartService->toArray();

    $totalPrice = (($product->price * $quantity) * (1 + ($product->tax_rate / 100))) + (($product2->price * $quantity) * (1 + ($product2->tax_rate / 100)));

    expect($cart['itemCount'])->toBe(2)
        ->and($cart['items'][$product->id]['quantity'])->toBe((float) $quantity)
        ->and($cart['items'][$product2->id]['quantity'])->toBe((float) $quantity)
        ->and($cart['total'])->toBe($totalPrice);
})->with([1, 2, 5, 10]);

test('can add item without quantity', function () {
    $product = createProduct();

    $this->post(route('cart.add', $product->id));

    $cart = $this->cartService->toArray();

    expect($cart['items'][1]['quantity'])->toBe(1.0);
});

test('cart is empty', function () {
    $cart = $this->cartService->toArray();

    expect($cart['itemCount'])->toBe(0)
        ->and($cart['items'])->toBe([])
        ->and($cart['total'])->toBe(0.0);
});

function createProduct(array $attributes = []): Product
{
    return Product::factory()->create([
        'name' => 'Test Product',
        'price' => 25.00,
        'tax_rate' => 20,
        ...$attributes,
    ]);
}
