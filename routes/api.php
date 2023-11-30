<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Procurement\ItemCategoryController;
use App\Http\Controllers\Procurement\ItemController;
use App\Http\Controllers\Projects\BudgetController;
use App\Http\Controllers\Projects\BudgetDetailController;
use App\Http\Resources\Auth\UserResource;
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

Route::post('/login', [ApiAuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return UserResource::make($request->user());
    });
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::post('/refresh', [ApiAuthController::class, 'refresh']);
    Route::apiResource('itemCategories', ItemCategoryController::class);
    Route::apiResource('items', ItemController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::apiResource('budgets.budgetDetails', BudgetDetailController::class);

    Route::get('budgets/{budget}/availableItems', [BudgetController::class, 'availableItems']);
});
