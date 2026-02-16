<?php

use App\Models\User;

test('login page is accessible', function () {
    $this->get(route('login.create'))->assertOk();
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('products.index'));
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('credentials');
    $this->assertGuest();
});

test('user cannot login without required fields', function () {
    $response = $this->post(route('login.store'), []);

    $response->assertSessionHasErrors(['email', 'password']);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post(route('logout'));

    $response->assertRedirect('/');
    $this->assertGuest();
});
