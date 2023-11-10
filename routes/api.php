<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Procurement\ItemCategoryController;
use App\Http\Controllers\Procurement\ItemController;
use App\Http\Controllers\Projects\BudgetController;
use App\Http\Controllers\Projects\BudgetDetailController;
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

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']],function () {
    Route::apiResource('itemCategories', ItemCategoryController::class);
    Route::apiResource('items', ItemController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::apiResource('budgets.budgetDetails', BudgetDetailController::class);
    
    Route::get('budgets/{budget}/availableItems', [BudgetController::class, 'availableItems']);
});
