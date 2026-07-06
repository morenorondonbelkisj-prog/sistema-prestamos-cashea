<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\PrestamoController;

Route::post('/prestamos', [PrestamoController::class, 'store']);

use App\Http\Controllers\PagoController;

Route::post('/pagos', [PagoController::class, 'store']);

use App\Http\Controllers\Api\ClienteController;

// Ruta API REST para el CRUD completo de Clientes
Route::apiResource('clientes', ClienteController::class);

use App\Http\Controllers\Api\PrestamoApiController;

// Ruta API REST para Préstamos y sus Cuotas automáticas
Route::apiResource('prestamos', PrestamoApiController::class);

use App\Http\Controllers\Api\PagoApiController;

// Ruta API REST para el flujo de Pagos y Facturación
Route::apiResource('pagos', PagoApiController::class);