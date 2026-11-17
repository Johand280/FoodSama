<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Order;
use App\OrderChannel;
use App\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'order_number' => 'PED-'.Str::upper(Str::random(6)),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'channel' => fake()->randomElement(OrderChannel::cases()),
            'status' => OrderStatus::Pending,
            'notes' => null,
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'cost_total' => 0,
            'profit' => 0,
            'completed_at' => null,
        ];
    }

    /**
     * Mark the order as completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => OrderStatus::Delivered,
            'completed_at' => now(),
            'delivered_at' => now(),
            'paid_at' => now(),
        ]);
    }
}
