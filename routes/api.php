<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CondominiumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\UnitController;
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

Route::middleware('auth:sanctum')->group(function () {
    // Administrator Routes
    Route::apiResource('condominium', CondominiumController::class)->only(['store', 'show']);
    Route::apiResource('unit', UnitController::class)->only(['index', 'store', 'update']);
    Route::apiResource('expense', ExpenseController::class)->only(['index', 'store']);
    Route::apiResource('income', IncomeController::class)->only(['index', 'store']);
    Route::get('dashboard', [DashboardController::class, 'index']);
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
