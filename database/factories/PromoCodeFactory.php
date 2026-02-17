<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromoCode>
 */
class PromoCodeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->regexify('[A-Z]{6}'),
            'discount' => $this->faker->numberBetween(5, 50),
            'used' => false,
            'expire_at' => now()->addMonth(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expire_at' => now()->subDay(),
        ]);
    }

    public function used(): static
    {
        return $this->state(fn () => [
            'used' => true,
        ]);
    }
}
