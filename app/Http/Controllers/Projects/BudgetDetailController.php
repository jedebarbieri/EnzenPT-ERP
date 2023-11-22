<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreBudgetDetailRequest;
use App\Http\Requests\Projects\UpdateBudgetDetailRequest;
use App\Http\Resources\Projects\BudgetDetailsResource;
use App\Models\Procurement\Item;
use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;

class BudgetDetailController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBudgetDetailRequest $request, Budget $budget)
    {
        try {
            $item = Item::findOrFail($request->input('item_id'));

            $validatedData = $request->validated();

            // Valores por defecto cuando se guarda por primera vez
            $data = array_merge([
                'budget_id' => $budget->id,
                'unit_price' => $item->unit_price,
                'sell_price' => $item->unit_price * (1 + $budget->gain_margin),
                'quantity' => 1,
                'tax_percentage' => 0.23,
            ], $validatedData);
            
            $budgetDetail = BudgetDetail::create($data);

            $response = ApiResponse::success(
                data: [
                    'budgetDetail' => BudgetDetailsResource::make($budgetDetail)
                ],
                message: 'Budget detail created successfully.'
            );
        } catch (\Exception $e) {
            $response = ApiResponse::error(
                message: 'Error creating budget detail',
                metadata: [
                    'errorDetails' => $e->getMessage()
                ]
            );
        }
        return $response->send();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $budgetDetail = BudgetDetail::with('item.itemCategory')->findOrFail($id);

            $response = ApiResponse::success(
                data: [
                    'budgetDetail' => BudgetDetailsResource::make($budgetDetail)
                ]
            );
        } catch (\Exception $e) {
            $response = ApiResponse::error(
                message: 'Error fetching budget detail',
                metadata: [
                    'errorDetails' => $e->getMessage()
                ]
            );
        }
        return $response->send();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBudgetDetailRequest $request, Budget $budget, BudgetDetail $budgetDetail)
    {
        try {
            // Obtener los datos validados del request
            $data = $validatedData = $request->validated();

            // Actualizar según el tipo de solicitud

            if (!$request->isMethod('patch')) {
                // Si el tipo de request es PUT, actualizar todo el modelo
                $data = array_merge([
                    'unit_price' => 0,
                    'quantity' => 0,
                    'tax_percentage' => 0,
                    'discount' => 0,
                    'sell_price' => 0,
                ], $validatedData);
            }

            $budgetDetail->update(
                collect($data)
                    // Excluir los campos que no se pueden actualizar
                    ->except('id, item_id, budget_id')
                    ->toArray()
            );

            $budgetDetail->setRelation('budget', []);
            $budgetDetail->setRelation('item.itemCategory', []);

            $response = ApiResponse::success(
                data: [
                    'budgetDetail' => BudgetDetailsResource::make($budgetDetail)
                ],
                message: 'Budget detail updated successfully.'
            );;
        } catch (\Exception $e) {
            $response = ApiResponse::error(
                message: 'Error updating budget detail',
                metadata: [
                    'errorDetails' => $e->getMessage()
                ]
            );
        }
        return $response->send();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget, BudgetDetail $budgetDetail)
    {
        try {
            $budgetDetail->delete();
            $response = ApiResponse::success(
                message: 'Budget detail deleted successfully.'
            );
        } catch (\Exception $e) {
            $response = ApiResponse::error(
                message: 'Error deleting budget detail',
                metadata: [
                    'errorDetails' => $e->getMessage()
                ]
            );
        }
        return $response->send();
    }
}
