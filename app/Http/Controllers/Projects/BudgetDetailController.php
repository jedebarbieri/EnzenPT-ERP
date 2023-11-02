<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\UpdateBudgetDetailRequest;
use App\Http\Resources\Projects\BudgetDetailsResource;
use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;
use Illuminate\Http\Request;

class BudgetDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBudgetDetailRequest $request, Budget $budget, BudgetDetail $budgetDetail)
    {

        // Verificar si la solicitud es de tipo PATCH
        $isPatchRequest = $request->isMethod('patch');

        // Obtener los datos validados del request
        $validatedData = $request->validated();

        // Actualizar según el tipo de solicitud
        if ($isPatchRequest) {
            // Si es una solicitud PATCH, actualizar solo los campos proporcionados
            $budgetDetail->update($validatedData);
        } else {
            // Si es una solicitud PUT, actualizar todo el modelo
            $budgetDetail->update(
                collect($validatedData)
                    ->except('id, item_id, budget_id') // Así puedo excluir algunos campos para la actualización?
                    ->toArray()
            );
        }
        $budgetDetail->setRelation('budget', []);
        $budgetDetail->setRelation('item.itemCategory', []);

        return response()->json([
            'status' => 'success',
            'message' => 'Budget detail updated successfully.',
            'data' => BudgetDetailsResource::make($budgetDetail)
        ]);
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
            ], 500);
        }
    }
}
