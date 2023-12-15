<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Procurement\ItemCategoryResource;
use App\Models\Procurement\ItemCategory;

class ItemCategoryController extends Controller
{
    public function index()
    {
        try {
            // Get all the main categories
            $mainCategories = ItemCategory::getAllMainCategories();

            // Transform the category collection using ItemCategoryResource
            $categoriesResource = ItemCategoryResource::collection($mainCategories);
            $response = ApiResponse::success(
                data: [
                    'itemCategories' => $categoriesResource,
                ]
            );
        } catch (\Throwable $th) {
            $response = ApiResponse::error(
                message: 'Error fetching categories',
                metadata: [
                    'errorDetails' => $th->getMessage()
                ],
                originalException: $th
            );
        }
        return $response->send();
    }
}
