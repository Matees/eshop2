<?php

use App\Cart\Contracts\CartInterface;
use App\Mail\OrderCreated;
use App\Models\Order;
use App\Models\PromoCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

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

    $response->assertRedirect(route('products.index'));

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

test('signed url allows access to order tracking', function () {
    $product = createProduct();
    $this->post(route('cart.add', $product->id));
    $this->post(route('orders.store'), createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
    ]));

    $order = Order::query()->first();
    $signedUrl = URL::signedRoute('orders.track', ['order' => $order->id]);

    $response = $this->get($signedUrl);

    $response->assertOk();
});

test('unsigned url returns 403 for order tracking', function () {
    $product = createProduct();
    $this->post(route('cart.add', $product->id));
    $this->post(route('orders.store'), createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
    ]));

    $order = Order::query()->first();

    $response = $this->get('/orders/track/'.$order->id);

    $response->assertForbidden();
});

test('order creation sends email via queue', function () {
    Mail::fake();

    $product = createProduct();
    $this->post(route('cart.add', $product->id));

    $this->post(route('orders.store'), createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
    ]));

    Mail::assertQueued(OrderCreated::class, function (OrderCreated $mail) {
        return $mail->hasTo('test@example.com');
    });
});

test('can verify valid promo code', function () {
    PromoCode::factory()->create([
        'code' => 'SAVE20',
        'discount' => 20,
    ]);

    $product = createProduct(['price' => 100, 'tax_rate' => 20]);
    $this->post(route('cart.add', $product->id));

    $response = $this->postJson('/cart/promo-code/verify', ['promo_code' => 'SAVE20']);

    $response->assertOk()
        ->assertJson([
            'valid' => true,
            'code' => 'SAVE20',
            'discount' => 20,
        ]);

    expect($response->json('discount_amount'))->toBeGreaterThan(0);
    expect($response->json('total'))->toBeGreaterThan(0);
});

test('cannot verify invalid promo code', function () {
    $product = createProduct();
    $this->post(route('cart.add', $product->id));

    $response = $this->postJson('/cart/promo-code/verify', ['promo_code' => 'INVALID']);

    $response->assertUnprocessable()
        ->assertJson(['valid' => false]);
});

test('cannot verify expired promo code', function () {
    PromoCode::factory()->expired()->create(['code' => 'EXPIRED10', 'discount' => 10]);

    $product = createProduct();
    $this->post(route('cart.add', $product->id));

    $response = $this->postJson('/cart/promo-code/verify', ['promo_code' => 'EXPIRED10']);

    $response->assertUnprocessable()
        ->assertJson(['valid' => false]);
});

test('cannot verify used promo code', function () {
    PromoCode::factory()->used()->create(['code' => 'USED10', 'discount' => 10]);

    $product = createProduct();
    $this->post(route('cart.add', $product->id));

    $response = $this->postJson('/cart/promo-code/verify', ['promo_code' => 'USED10']);

    $response->assertUnprocessable()
        ->assertJson(['valid' => false]);
});

test('order with promo code saves discount', function () {
    $promoCode = PromoCode::factory()->create([
        'code' => 'SAVE20',
        'discount' => 20,
    ]);

    $product = createProduct(['price' => 100, 'tax_rate' => 20]);
    $this->post(route('cart.add', $product->id), ['quantity' => 1]);

    $subtotal = $this->cartService->getSubtotal();
    $expectedDiscount = round($subtotal * 0.20, 2);

    $response = $this->post(route('orders.store'), createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
        'promo_code' => 'SAVE20',
    ]));

    $response->assertRedirect(route('products.index'));

    $order = Order::query()->first();

    expect((float) $order->discount_amount)->toBe($expectedDiscount)
        ->and((int) $order->promo_code_id)->toBe($promoCode->id)
        ->and($promoCode->refresh()->used)->toBeTrue();
});

test('order without promo code works as before', function () {
    $product = createProduct(['price' => 100, 'tax_rate' => 20]);
    $this->post(route('cart.add', $product->id), ['quantity' => 2]);

    $cartTotal = $this->cartService->getTotal();

    $this->post(route('orders.store'), createOrder([
        'email' => 'test@example.com',
        'city' => 'Cabaj',
        'street' => 'Zahradna',
        'houseNumber' => '752/2',
        'zip' => '95117',
    ]));

    $order = Order::query()->first();
    expect((float) $order->discount_amount)->toBe(0.0)
        ->and($order->promo_code_id)->toBeNull()
        ->and((float) $order->total)->toBe((float) $cartTotal);
});
