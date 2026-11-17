<?php

namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company().' Food';

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(2).'-'.fake()->numberBetween(100, 999),
            'phone' => '+57 300 123 4567',
            'email' => fake()->unique()->companyEmail(),
            'address' => fake()->streetAddress(),
            'tax_id' => fake()->optional()->numerify('###########'),
            'tax_rate' => 0,
            'currency' => 'COP',
        ];
    }
}
