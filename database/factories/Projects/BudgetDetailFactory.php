<?php

namespace Database\Factories\Projects;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Projects\BudgetDetail>
 */
class BudgetDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_price' => $this->faker->randomFloat(2, 1, 100),
            'quantity' => $this->faker->randomFloat(0,1,10),
            'tax_percentage' => 23,
            'sell_price' => $this->faker->randomFloat(2, 10, 100),
            'discount' => $this->faker->randomFloat(2, 0, 5),
        ];
    }
}
