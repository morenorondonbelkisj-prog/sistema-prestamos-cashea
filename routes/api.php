<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\PrestamoApiController;
use App\Http\Controllers\Api\PagoApiController;
use App\Http\Controllers\PrestamoController;

/*
|--------------------------------------------------------------------------
| Rutas API REST (Modo Pruebas)
|--------------------------------------------------------------------------
*/

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('prestamos', PrestamoApiController::class);
Route::apiResource('pagos', PagoApiController::class);

// Ruta personalizada para registrar el pago de una cuota
Route::post('/cuotas/{id}/pagar', [PagoApiController::class, 'pagarCuota']);

// ======================================================================
// RUTAS PARA EL ENTREGABLE IV (OLAP & AUDITORÍA)
// ======================================================================
Route::get('/olap/resumen-cartera', [PrestamoController::class, 'obtenerResumenOlap']);
Route::get('/auditoria', [PrestamoController::class, 'obtenerAuditoria']);
Route::put('/prestamos/{id}/estado', [PrestamoController::class, 'actualizarEstadoPrestamo']);