<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 500);
        $taxAmount = $subtotal * 0.2;
        $discountAmount = $this->faker->randomFloat(2, 0, 20);
        $total = $subtotal + $taxAmount - $discountAmount;

        return [
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'address_line_one' => $this->faker->streetAddress(),
            'address_line_two' => $this->faker->city(),
            'address_line_three' => $this->faker->postcode(),
            'status' => OrderStatus::Pending->value,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'paid_at' => now(),
        ]);
    }

    public function withNote(?string $note = null): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => $note ?? $this->faker->sentence(),
        ]);
    }

    public function withInvalidEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'invalid-email',
        ]);
    }
}
