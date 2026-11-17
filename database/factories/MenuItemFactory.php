<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
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
            'category' => fake()->randomElement(['Platos principales', 'Entradas', 'Bebidas', 'Postres']),
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 60),
            'cost' => fake()->randomFloat(2, 2, 25),
            'is_available' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
