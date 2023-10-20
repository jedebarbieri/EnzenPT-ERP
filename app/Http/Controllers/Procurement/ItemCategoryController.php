<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\ItemCategory;

class ItemCategoryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        // Obtener todas las categorías principales
        $mainCategories = ItemCategory::whereNull(ItemCategory::PARENT_COLUMN_NAME)->with('children')->get();

        // Estructura de dos niveles
        $categoriesHierarchy = [];

        // Iterar sobre las categorías principales
        foreach ($mainCategories as $mainCategory) {
            $mainCategoryData = $mainCategory->toArray();

            // Agregar las categorías hijas
            $mainCategoryData['children'] = $mainCategory->children->toArray();

            // Agregar la categoría principal al arreglo final
            $categoriesHierarchy[] = $mainCategoryData;
        }

        return response()->json([
            'status' => 'success',
            'data' => $categoriesHierarchy
        ]);
    }

}
