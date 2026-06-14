<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:CC'])->group(function () {
    Route::get('/dashboard', [QueueController::class, 'index']);
    Route::post('/orders/accept', [OrderController::class, 'accept']);
    Route::post('/orders/void', [OrderController::class, 'void']);
    Route::get('/order-types', [OrderTypeController::class, 'index']);
});
