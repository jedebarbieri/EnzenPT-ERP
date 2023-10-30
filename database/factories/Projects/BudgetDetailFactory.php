<?php

namespace Database\Factories\Projects;

use App\Models\Projects\BudgetDetail;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Projects\BudgetDetail>
 */
class BudgetDetailFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_price' => $this->faker->randomFloat(2, 1, 100),
            'quantity' => $this->faker->randomFloat(0, 1, 10),
            'tax_percentage' => 23,
            'sell_price' => $this->faker->randomFloat(2, 10, 100),
            'discount' => $this->faker->boolean(5) ? $this->faker->randomFloat(2, 0, 5) : 0,
        ];
    }

    /**
     * Configure the model after being instantiated.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (BudgetDetail $budgetDetail) {

            $unitPrice = $this->faker->randomFloat(2, 1, 100);
            $sellPrice = $this->faker->randomFloat(2, 10, 100);
            $discount = $this->faker->boolean(5) ? $this->faker->randomFloat(2, 0, 5) : 0;

            if (empty($budgetDetail->budget)) {
                throw new Exception("The reference budget is empty.");
            }
            if (empty($budgetDetail->item)) {
                throw new Exception("The reference item is empty.");
            }

            // 70% de las veces el unit_price es igual al item al que está referenciando
            if ($this->faker->boolean(70)) {
                $unitPrice = $budgetDetail->item->unit_price;
            } else {
                // 30% de las veces con variación del precio unitario original
                $variation = $this->faker->randomFloat(2, 0.05, 0.2);
                $unitPrice = $budgetDetail->item->unit_price + ($budgetDetail->item->unit_price * $variation * ($this->faker->boolean() ? 1 : -1));
            }

            // 90% de las veces el sell_price es como la referencia
            if ($this->faker->boolean(90)) {
                $sellPrice = $budgetDetail->item->unit_price * ($budgetDetail->budget->gain_margin + 1);
            } else {
                // 10% de las veces variación mínima del margen de ganancia
                $sellPrice = $budgetDetail->item->unit_price * ($budgetDetail->budget->gain_margin + $this->faker->randomFloat(2, 0, 0.1));
            }

            $budgetDetail->unit_price = $unitPrice;
            $budgetDetail->sell_price = $sellPrice;
            $budgetDetail->discount = $discount;
            $budgetDetail->save();
        });
    }
}
