<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\FacturaPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PagoApiController extends Controller
{
    /**
     * EVIDENCIA: API REST - Listar el historial de pagos realizados
     */
    public function index()
    {
        $pagos = Pago::with('factura')->get();
        return response()->json([
            'success' => true,
            'data' => $pagos
        ], 200);
    }

    /**
     * EVIDENCIA: API REST & Relaciones ORM - Procesar Pago de Cuota y Facturar
     */
    public function store(Request $request)
    {
        // 1. Validamos que la cuota exista
        $validator = Validator::make($request->all(), [
            'cuota_id' => 'required|exists:cuotas,id', // Ajusta 'id' si tu llave primaria de cuota se llama distinto
            'monto_pagado' => 'required|numeric|min:0.01',
            'metodo_pago'  => 'required|string|max:50', // Ej. Transferencia, Pago Móvil, Efectivo
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Iniciamos la transacción en PostgreSQL
        DB::beginTransaction();

        try {
            // Buscamos la cuota para verificar su estado
            $cuota = Cuota::findOrFail($request->cuota_id);

            if ($cuota->estado === 'pagado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta cuota ya se encuentra totalmente cancelada.'
                ], 400);
            }

            // 3. ORM: Registrar el Pago
            $pago = Pago::create([
                'cuota_id'     => $cuota->id,
                'monto'        => $request->monto_pagado,
                'fecha_pago'   => Carbon::now()->toDateString(),
                'metodo_pago'  => $request->metodo_pago,
            ]);

            // 4. Actualizar el estado de la cuota usando el ORM
            $cuota->update([
                'estado' => 'pagado'
            ]);

            // 5. Relaciones ORM: Crear la factura ligada directamente al pago recién hecho
            $factura = $pago->factura()->create([
                'numero_factura' => 'FAC-' . time(), // Generamos un número único basado en el tiempo
                'fecha_emision'  => Carbon::now()->toDateTimeString(),
                'monto_total'    => $request->monto_pagado,
            ]);

            // Si todo fue exitoso, confirmamos en PostgreSQL
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado con éxito y factura emitida.',
                'data' => [
                    'pago' => $pago,
                    'cuota_actualizada' => $cuota,
                    'factura' => $factura
                ]
            ], 201);

        } catch (\Exception $e) {
            // En caso de error, revertimos para no dejar datos inconsistentes
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago transaccional',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}