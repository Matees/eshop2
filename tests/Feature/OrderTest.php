<?php

use App\Cart\Contracts\CartInterface;
use App\Models\Order;

beforeEach(function () {
    $this->cartService = app(CartInterface::class);
});

test('can create order', function () {
    $product = createProduct();
    $order = createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
    ]);

    $this->post(route('cart.add', $product->id), ['quantity' => 2]);

    $response = $this->post(route('orders.store'), $order);

    $response->assertRedirect('/');

    $this->assertDatabaseHas('orders', [
        'email' => $order['email'],
        'address_line_one' => $order['street'].', '.$order['houseNumber'],
        'address_line_two' => $order['city'],
        'address_line_three' => $order['zip'],
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('cannot create order without required fields', function () {
    $response = $this->post(route('orders.store'), []);

    $response->assertSessionHasErrors(['email', 'city', 'street', 'houseNumber', 'zip']);
});

test('cannot create order with invalid email', function () {
    $response = $this->post(route('orders.store'), Order::factory()->withInvalidEmail()->make()->toArray());

    $response->assertSessionHasErrors(['email']);
});

test('cannot create order with empty cart', function () {
    createOrder();

    $this->assertDatabaseCount('orders', 0);
});

test('cart is cleared after order creation', function () {
    $product = createProduct();
    $order = createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
    ]);

    $this->post(route('cart.add', $product->id));

    $this->post(route('orders.store'), $order);

    expect(count($this->cartService->getItems()))->toBe(0);
});

test('order total matches cart total', function () {
    $product = createProduct(['price' => 100, 'tax_rate' => 20]);
    $order = createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
    ]);

    $this->post(route('cart.add', $product->id), ['quantity' => 2]);

    $cartTotal = $this->cartService->getTotal();

    $this->post(route('orders.store'), $order);

    $this->assertDatabaseHas('orders', [
        'email' => 'test@example.com',
        'total' => $cartTotal,
    ]);
});
