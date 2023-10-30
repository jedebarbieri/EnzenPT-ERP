<?php

use App\Http\Controllers\Procurement\ItemCategoryController;
use App\Http\Controllers\Procurement\ItemController;
use App\Http\Controllers\Projects\BudgetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'items' => ItemController::class,
    'itemCategories' => ItemCategoryController::class,
    'budgets' => BudgetController::class,
    'budgets.budgetDetails' => BudgetDetailController::class,
]);
