<?php

use App\Http\Controllers\Projects\BudgetController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth']], function () {
    
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/procurement', function() {
        return view('procurement');
    })->name('procurement');

    Route::get('/budgets', function() {
        return view('budgets');
    })->name('budgetsHome');

    Route::get('/budgetReport/{budget}', [BudgetController::class, 'createReport'])->name('budgetReport');
});

Auth::routes();