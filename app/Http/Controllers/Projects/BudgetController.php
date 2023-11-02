<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\UpdateBudgetRequest;
use App\Http\Requests\Projects\StoreBudgetRequest;
use App\Http\Resources\Projects\BudgetResource;
use App\Models\Projects\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $columns = [
            'id',
            'name',
            'status',
            'updated_at'
        ];

        $query = Budget::without('budgetDetails');

        // Applying sort
        if ($request->has('order')) {
            $order = intval($request->input('order.0.column'));
            $dir = $request->input('order.0.dir');
            $query->orderBy($columns[$order], $dir);
        }

        // Applying filter
        if ($request->has('search.value')) {
            $searchValue = $request->input('search.value');
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%' . $searchValue . '%');
            }
        }

        // Pagination
        $length = intval($request->input('length')) ?: Controller::PAGINATION_DEFAULT_PER_PAGE;
        $start = intval($request->input('start', 1));
        $budgets = $query->offset($start)->limit($length);

        // Obtener la página actual desde la solicitud
        $page = intval($start / $length) + 1;

        // Paginar la consulta con el length y la página
        $budgets = $query->paginate($length, ['*'], 'page', $page);

        $budgets->each(function ($budget) {
            $budget->setRelation('budgetDetails', []);
        });

        // Transformar la colección utilizando ItemResource
        $budgetsResource = BudgetResource::collection($budgets);

        return response()->json([
            'status' => 'success',
            'data' => $budgetsResource,
            'metadata' => [
                'recordsFiltered' => $budgets->total(),
                'recordsTotal' => $budgets->total(),
                'draw' => $request->input('draw') ?: 1,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBudgetRequest $request)
    {
        $budget = Budget::create($request->validated());
        $budget->setRelation('budgetDetails', []);
        return response()->json([
            'status' => 'success',
            'data' => [
                'budget' => BudgetResource::make($budget)
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        try {

            // DB::enableQueryLog();
            // Load the budget details relationship and ordered by internal_cod
            $budget = Budget::with([
                'budgetDetails' => function ($query) {
                    $query->join('items', 'budget_details.item_id', '=', 'items.id')
                        ->select(
                            "*",
                            "budget_details.unit_price as overwriten_unit_price",
                            "items.unit_price as item_unit_price",
                            "budget_details.id as budget_detail_id",
                        )
                        ->orderBy('items.internal_cod')
                        ->with('item.itemCategory');
                }
            ])->find($budget->id);
            
            // $queries = DB::getQueryLog();

            foreach ($budget->budgetDetails as $budgetDetail) {
                $budgetDetail->id = $budgetDetail->budget_detail_id;
                $budgetDetail->unit_price = $budgetDetail->overwriten_unit_price;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'budget' => new BudgetResource($budget)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBudgetRequest $request, Budget $budget)
    {

        // Verificar si la solicitud es de tipo PATCH
        $isPatchRequest = $request->isMethod('patch');

        // Obtener los datos validados del request
        $validatedData = $request->validated();

        // Actualizar según el tipo de solicitud
        if ($isPatchRequest) {
            // Si es una solicitud PATCH, actualizar solo los campos proporcionados
            $budget->update($validatedData);
        } else {
            // Si es una solicitud PUT, actualizar todo el modelo
            $budget->update(
                collect($validatedData)
                    ->except('id')
                    ->toArray()
            );
        }
        $budget->setRelation('budgetDetails', []);

        return response()->json([
            'status' => 'success',
            'message' => 'Budget updated successfully.',
            'data' => BudgetResource::make($budget)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
