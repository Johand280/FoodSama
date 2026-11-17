<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Receipt>
 */
class ReceiptFactory extends Factory
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
            'order_id' => Order::factory(),
            'number' => 'C-'.Str::upper(Str::random(6)),
            'issued_at' => now(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
        ];
    }
}
