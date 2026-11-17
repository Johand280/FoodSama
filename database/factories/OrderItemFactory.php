<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 5, 40);

        return [
            'order_id' => Order::factory(),
            'business_id' => Business::factory(),
            'menu_item_id' => null,
            'name' => fake()->words(2, true),
            'quantity' => fake()->numberBetween(1, 4),
            'unit_price' => $price,
            'unit_cost' => round($price * 0.6, 2),
        ];
    }
}
