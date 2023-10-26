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
        $columns = [
            'id',
            'name',
            'internal_cod',
            'unit_price'
        ];

        $query = Item::query()->with('category');

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
            'data' => $itemsResource,
            'metadata' => [
                'recordsFiltered' => $items->total(),
                'recordsTotal' => $items->total(),
                'draw' => $request->input('draw') ?: 1,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->validated());
        return response()->json([
            'status' => 'success',
            //'message' => 'Item successfully created.',
            'data' => [
                'item' => ItemResource::make($item)
            ],
            // 'metadata' => [

            // ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update(
            collect($request->validated())
                ->except('id')
                ->toArray()
        );
    
        return response()->json([
            'status' => 'success',
            'message' => 'Item updated successfully.',
            'data' => new ItemResource($item),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Verificar si el item existe
        if ($item) {
            // Marcar el item como eliminado (soft delete)
            $item->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item successfully deleted.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Item was not found.'
            ], 404); // 404 Not Found
        }
    }
}
