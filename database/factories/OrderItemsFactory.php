<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItems>
 */
class OrderItemsFactory extends Factory
{
    protected $model = OrderItems::class;

    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 5, 100);
        $quantity = $this->faker->numberBetween(1, 5);
        $taxRate = $this->faker->randomFloat(2, 0, 0.25);
        $total = $unitPrice * $quantity * (1 + $taxRate);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }
}
