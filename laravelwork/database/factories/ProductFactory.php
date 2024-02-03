<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'id' => fake()->unique()->numberBetween(1,30), // unique()必須
            'shop_id' => fake()->numberBetween(1,30),
            'name' => fake()->word(7),
            'price' => fake()->numberBetween(100, 10000),
            'stock' => fake()->numberBetween(50, 100),
            'discription' => fake()->realText(50),

        ];
    }
}
