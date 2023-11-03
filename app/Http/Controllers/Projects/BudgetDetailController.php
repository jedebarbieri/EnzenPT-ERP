<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreBudgetDetailRequest;
use App\Http\Requests\Projects\UpdateBudgetDetailRequest;
use App\Http\Resources\Projects\BudgetDetailsResource;
use App\Models\Procurement\Item;
use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;

class BudgetDetailController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }


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
            
            return response()->json([
                'status' => 'success',
                'message' => 'Budget detail created successfully.',
                'data' => [
                    'budgetDetail' => BudgetDetailsResource::make($budgetDetail)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Budget detail could not be created.' . $e->getMessage(),
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $budgetDetail = BudgetDetail::with('item.itemCategory')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => BudgetDetailsResource::make($budgetDetail)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
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

            return response()->json([
                'status' => 'success',
                'message' => 'Budget detail updated successfully.',
                'data' => BudgetDetailsResource::make($budgetDetail)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Budget detail could not be updated.',
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget, BudgetDetail $budgetDetail)
    {
        try {
            $budgetDetail->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Budget detail deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Budget detail could not be deleted.',
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
