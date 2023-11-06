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
        $createdAt = $this->faker->dateTimeBetween('-3 years', 'now -2 months');
        $updatedAt = $this->faker->dateTimeBetween($createdAt->modify('+2 months'), 'now');
    
        $randomNumber = $this->faker->numberBetween(1, 100);
    
        if ($randomNumber <= 5) {
            $status = Budget::STATUS_REJECTED;
        } elseif ($randomNumber <= 35) {
            $status = Budget::STATUS_APPROVED;
        } else {
            $status = Budget::STATUS_DRAFT;
        }

        return [
            'status' => $status,
            'name' => ucwords($this->faker->word),
            'gain_margin' => $this->faker->randomFloat(4, 0.05, 0.3),
            'project_name' => ucwords($this->faker->words(3, true)),
            'project_number' => $this->faker->regexify('[A-Z0-9]{5}'),
            'project_location' => fake('pt_PT')->city,
            'total_peak_power' => $this->faker->randomFloat(2, 100, 1000),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
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
