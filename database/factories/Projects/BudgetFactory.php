<?php

namespace Database\Factories\Projects;

use App\Models\Projects\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{

    public function definition()
    {
        return [
            'status' => fake()->randomElement([Budget::STATUS_DRAFT, Budget::STATUS_APPROVED, Budget::STATUS_REJECTED]),
            'name' => ucwords(fake()->word),
            'gain_margin' => fake()->randomFloat(2, 0.05, 0.5),
            'project_name' => ucwords(fake()->words(3, true)),
            'project_number' => fake()->regexify('[A-Z0-9]{5}'),
            'project_location' => fake('pt_PT')->city,
            'total_power_pick' => fake()->randomFloat(2, 100, 1000),
        ];
    }
}