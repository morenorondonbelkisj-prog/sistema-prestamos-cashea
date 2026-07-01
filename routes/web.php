<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\PrestamoController;

Route::post('/prestamos', [PrestamoController::class, 'store']);

use App\Http\Controllers\PagoController;

Route::post('/pagos', [PagoController::class, 'store']);