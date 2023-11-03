<?php

namespace Database\Factories\Projects;

use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition()
    {
        return [
            'status' => $this->faker->randomElement([Budget::STATUS_DRAFT, Budget::STATUS_APPROVED, Budget::STATUS_REJECTED]),
            'name' => ucwords($this->faker->word),
            'gain_margin' => $this->faker->randomFloat(2, 0.05, 0.3),
            'project_name' => ucwords($this->faker->words(3, true)),
            'project_number' => $this->faker->regexify('[A-Z0-9]{5}'),
            'project_location' => fake('pt_PT')->city,
            'total_power_pick' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }

    // Método state para generar detalles del presupuesto después de la creación
    public function configure()
    {
        return $this->afterCreating(function (Budget $budget) {

            // Generar una lista de ítems aleatorios
            $itemIds = range(1, 168);
            shuffle($itemIds);

            // Obtener una cantidad aleatoria de ítems para asociar al presupuesto
            $numberOfItems = rand(2, 20);
            $selectedItemIds = array_slice($itemIds, 0, $numberOfItems);

            // Crear detalles del presupuesto asociados con los ítems seleccionados
            foreach ($selectedItemIds as $itemId) {
                BudgetDetail::factory()->create([
                    'budget_id' => $budget->id,
                    'item_id' => $itemId,
                ]);
            }
        });
    }
}
