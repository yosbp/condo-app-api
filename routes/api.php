<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CondominiumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ReportedPaymentController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitTypeController;
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
    Route::apiResource('condominium', CondominiumController::class)->only(['index', 'store', 'show']);
    Route::apiResource('unit', UnitController::class)->only(['index', 'store', 'update']);
    Route::apiResource('expense', ExpenseController::class)->only(['index', 'store']);
    Route::apiResource('income', IncomeController::class)->only(['index', 'store']);
    Route::apiResource('unit-type', UnitTypeController::class)->only(['index', 'store']);
    Route::apiResource('expense-category', ExpenseCategoryController::class)->only(['index', 'store']);
    Route::apiResource('reported-payment', ReportedPaymentController::class)->only(['index', 'store']);
    Route::apiResource('owner', OwnerController::class)->only(['index', 'store']);
    Route::get('dashboard', [DashboardController::class, 'index']); // Get dashboard data
    route::post('owner/assign-unit', [OwnerController::class, 'assignUnit']); // Assign unit to owner
    Route::post('owner/approve', [OwnerController::class, 'approveOwner']); // Approve owner
    Route::post('owner/unlink', [OwnerController::class, 'unlinkOwner']); // Unlink owner
    Route::get('me', [OwnerController::class, 'me']); // Get current Owner
    Route::get('data-to-invoice', [CondominiumController::class, 'dataToInvoice']); // Get data to invoice
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
