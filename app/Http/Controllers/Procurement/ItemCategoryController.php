<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\ApiResponse;
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
            $response = ApiResponse::success(
                data: [
                    'categoryList' => $categoriesResource,
                ]
            );
        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: 'Error fetching categories',
                metadata: [
                    'errorDetails' => $th->getMessage()
                ]
            );
        }
        return $response->send();
    }
}
