<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\UpdateBudgetRequest;
use App\Http\Requests\Projects\StoreBudgetRequest;
use App\Http\Resources\Projects\BudgetResource;
use App\Models\Projects\Budget;
use Illuminate\Http\Request;

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
            // Load the budget details relationship

            $budget->load('budgetDetails.item.itemCategory');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'budget' => new BudgetResource($budget)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
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
        $budget->update(
            collect($request->validated())
                ->except('id')
                ->toArray()
        );
    
        return response()->json([
            'status' => 'success',
            'message' => 'Budget updated successfully.',
            'data' => new BudgetResource($budget),
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
