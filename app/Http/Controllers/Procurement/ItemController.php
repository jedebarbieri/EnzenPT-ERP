<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\StoreItemRequest;
use App\Http\Requests\Procurement\UpdateItemRequest;
use App\Http\Resources\Procurement\ItemResource;
use App\Models\Procurement\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $columns = [
                'items.id',
                'internal_cod',
                'items.name',
                'item_category.name',
                'unit_price'
            ];
    
            $query = Item::query();
            
            $query->join('item_categories as item_category', 'items.item_category_id', '=', 'item_category.id');
    
            // Applying sort
            if ($request->has('order') && $request->has('order.column')) {
                $dir = $request->has('order.dir') ? $request->input('order.dir') : 'asc';
                $query->orderBy(Str::snake($request->input('order.column')), $dir);
            } else {
                $query->orderBy('items.internal_cod', 'asc');
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
            $items = $query->paginate($length, ['items.*'], 'page', $page);
    
            // Transformar la colección utilizando ItemResource
            $itemsResource = ItemResource::collection($items);
    
            $response = ApiResponse::success(
                data: [
                    'items' => $itemsResource,
                    'recordsFiltered' => $items->total(),
                    'recordsTotal' => $items->total(),
                ],
                metadata: [
                    'draw' => $request->input('draw') ?: 1,
                ]
            );

        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: 'Error fetching items',
                metadata: [
                    'errorDetails' => $th->getMessage()
                ],
                originalException: $th
            );
        }
        return $response->send();
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
            $response = ApiResponse::success(
                data: [
                    'item' => ItemResource::make($item)
                ],
                message: 'Item created successfully.'
            );
        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: 'Error creating item',
                metadata: [
                    'errorDetails' => $th->getMessage()
                ],
                originalException: $th
            );
        }
        return $response->send();
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
            
            $response = ApiResponse::success(
                data: [
                    'item' => ItemResource::make($item)
                ],
                message: 'Item updated successfully.'
            );

        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: 'Error updating item',
                metadata: [
                    'errorDetails' => $th->getMessage()
                ],
                originalException: $th
            );
        }
        return $response->send();
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
            $response = ApiResponse::success(
                message: 'Item deleted successfully.'
            );

        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: 'Error deleting item',
                metadata: [
                    'errorDetails' => $th->getMessage()
                ],
                originalException: $th
            );
        }
        return $response->send();
    }
}
