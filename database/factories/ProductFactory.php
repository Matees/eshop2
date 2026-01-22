<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'image' => 'https://picsum.photos/640/480?random=' . $this->faker->unique()->randomNumber(),
            'description' => $this->faker->sentence(),
            'price' => 20.00,
            'tax_rate' => $this->faker->numberBetween( 1,100),
        ];
    }
}
