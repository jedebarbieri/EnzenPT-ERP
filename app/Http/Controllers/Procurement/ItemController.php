<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\StoreItemRequest;
use App\Http\Requests\Procurement\UpdateItemRequest;
use App\Http\Resources\Procurement\ItemResource;
use App\Models\Procurement\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
                'internal_cod',
                'unit_price'
            ];
    
            $query = Item::query()->with('itemCategory');
    
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
            $items = $query->offset($start)->limit($length);
    
            // Obtener la página actual desde la solicitud
            $page = intval($start / $length) + 1;
    
            // Paginar la consulta con el length y la página
            $items = $query->paginate($length, ['*'], 'page', $page);
    
            // Transformar la colección utilizando ItemResource
            $itemsResource = ItemResource::collection($items);
    
            return response()->json([
                'status' => 'success',
                'data' => [
                    'itemList' => $itemsResource,
                ],
                'metadata' => [
                    'recordsFiltered' => $items->total(),
                    'recordsTotal' => $items->total(),
                    'draw' => $request->input('draw') ?: 1,
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching items',
                'metadata' => [
                    'errorDetails' => $th->getMessage()
                ],
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Valores por defecto cuando se guarda por primera vez
            $data = array_merge([
                'internal_cod' => '',
                'name' => '',
                'unit_price' => 0,
            ], $validatedData);

            $item = Item::create($data);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'item' => ItemResource::make($item)
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating item',
                'metadata' => [
                    'errorDetails' => $th->getMessage()
                ],
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        try {

            // Obtener los datos validados del request
            $data = $validatedData = $request->validated();

            // Actualizar según el tipo de solicitud
            if (!$request->isMethod('patch')) {
                // Si el tipo de request es PUT, actualizar todo el modelo
                $data = array_merge([
                    'internal_cod' => '',
                    'name' => '',
                    'unit_price' => 0,
                ], $validatedData);
            }

            $item->update(
                collect($data)
                    ->except('id')
                    ->toArray()
            );
        
            return response()->json([
                'status' => 'success',
                'message' => 'Item updated successfully.',
                'data' => [
                    'item' => new ItemResource($item),
                ],
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating item',
                'metadata' => [
                    'errorDetails' => $th->getMessage()
                ],
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try {
            // Verificar si el item existe
            if (!$item) {
                throw new \Exception('Item was not found.');
            }
            
            // Marcar el item como eliminado (soft delete)
            $item->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item successfully deleted.'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching items',
                'metadata' => [
                    'errorDetails' => $th->getMessage()
                ],
            ], 500);
        }
    }
}
