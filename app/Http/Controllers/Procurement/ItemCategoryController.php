<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Procurement\ItemCategoryResource;
use App\Models\Procurement\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
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

    public function index()
    {
        // Obtener todas las categorías principales
        $mainCategories = ItemCategory::whereNull(ItemCategory::PARENT_COLUMN_NAME)->with('children')->get();

        // Transformar la colección de categorías usando ItemCategoryResource
        $categoriesResource = ItemCategoryResource::collection($mainCategories);

        return response()->json([
            'status' => 'success',
            'data' => $categoriesResource
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemCategory $itemCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemCategory $itemCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemCategory $itemCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemCategory $itemCategory)
    {
        //
    }
}
