<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\UpdateBudgetRequest;
use App\Http\Requests\Projects\StoreBudgetRequest;
use App\Http\Resources\Procurement\ItemResource;
use App\Http\Resources\Projects\BudgetResource;
use App\Models\Procurement\Item;
use App\Models\Projects\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
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
            } else {
                $query->orderByRaw("FIELD(status, " . Budget::STATUS_DRAFT . ", " . Budget::STATUS_APPROVED . ", " . Budget::STATUS_REJECTED . ")")
                    ->orderBy('updated_at', 'desc');
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
                'data' => [
                    'budgetList' => $budgetsResource,
                ],
                'metadata' => [
                    'recordsFiltered' => $budgets->total(),
                    'recordsTotal' => $budgets->total(),
                    'draw' => $request->input('draw') ?: 1,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching budgets',
                'metadata' => [
                    'errorDetails' => $th->getMessage()
                ],
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBudgetRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Valores por defecto cuando se guarda por primera vez
            $data = array_merge([
                'project_name' => '',
                'project_number' => '',
                'project_location' => '',
                'total_peak_power' => 0,
                'gain_margin' => 0,
                'status' => Budget::STATUS_DRAFT
            ], $validatedData);

            $budget = Budget::create($data);
            $budget->setRelation('budgetDetails', []);

            return response()->json([
                'status' => 'success',
                'message' => 'Budget created successfully.',
                'data' => [
                    'budget' => BudgetResource::make($budget)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Budget could not be created.',
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
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
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBudgetRequest $request, Budget $budget)
    {

        try {

            // Obtener los datos validados del request
            $data = $validatedData = $request->validated();

            // Actualizar según el tipo de solicitud
            if (!$request->isMethod('patch')) {
                // Si el tipo de request es PUT, actualizar todo el modelo
                $data = array_merge([
                    'status' => Budget::STATUS_DRAFT,
                    'name' => "",
                    'gain_margin' => 0,
                    'project_name' => "",
                    'project_number' => "",
                    'project_location' => "",
                    'total_peak_power' => 0,
                ], $validatedData);
            }

            $budget->update(
                collect($data)
                    // Excluir los campos que no se pueden actualizar
                    ->except('id')
                    ->toArray()
            );

            $budget->setRelation('budgetDetails', []);

            return response()->json([
                'status' => 'success',
                'message' => 'Budget updated successfully.',
                'data' => [
                    'budget' => BudgetResource::make($budget)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Budget could not be updated.' . $e->getMessage(),
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        try {
            $budget->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Budget deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Budget could not be deleted.',
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function availableItems(Budget $budget)
    {
        try {
            // Obtener todos los elementos que no están en la tabla budget_details para el budgetId especificado
            $items = Item::whereNotIn('id', function ($query) use ($budget) {
                $query->select('item_id')
                    ->from('budget_details')
                    ->where('budget_id', $budget->id);
            })->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'itemList' => ItemResource::collection($items)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error while retrieving available items.',
                'metadata' => [
                    'errorDetails' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
