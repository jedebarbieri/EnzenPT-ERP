<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Procurement\ItemCategoryResource;
use App\Models\Procurement\ItemCategory;

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
        try {
            // Get all the main categories
            $mainCategories = ItemCategory::getAllMainCategories();

            // Transform the category collection using ItemCategoryResource
            $categoriesResource = ItemCategoryResource::collection($mainCategories);

            return response()->json([
                'status' => 'success',
                'data' => $categoriesResource
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching categories',
                'metadata' => [
                    'errorDetails' => $th->getMessage()
                ],
            ], 500);
        }
    }
}
