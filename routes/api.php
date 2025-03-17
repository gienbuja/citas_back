<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('/me', [AuthController::class,'me']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::resource('/citas', CitaController::class);
    Route::get('/users', [AuthController::class, 'getUsers']);
    Route::get('users/change/{user}', [AuthController::class,'userChangeRol']);
    Route::get('dashboard/mes', [DashboardController::class,'getCitasPorMes']);
});