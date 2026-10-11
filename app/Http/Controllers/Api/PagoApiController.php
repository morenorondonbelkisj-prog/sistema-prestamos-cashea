<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuota;
use App\Models\Pago; // <--- ¡Esta línea es la que falta y genera el error!
use App\Models\PrestamoFinanciero;
use App\Http\Resources\Api\CuotaResource;
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
        $pagos = Pago::with('factura')->orderBy('id_pago', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $pagos
        ], 200);
    }

    /**
     * EVIDENCIA: API REST & Relaciones ORM - Procesar Pago de Cuota y Facturar
     */
    public function store(Request $request)
    {
        // 1. Validamos la petición usando id_cuota
        $validator = Validator::make($request->all(), [
            'id_cuota'            => 'required|exists:cuotas,id_cuota',
            'monto_pagado'        => 'required|numeric|min:0.01',
            'metodo_pago'         => 'required|string|max:50',
            'referencia_bancaria' => 'nullable|string|max:100',
            'banco_origen'        => 'nullable|string|max:100',
            'observaciones'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Ejecutamos la transacción en PostgreSQL
        try {
            $resultado = DB::transaction(function () use ($request) {
                // Buscamos la cuota con bloqueo para evitar condiciones de carrera
                $cuota = Cuota::where('id_cuota', $request->id_cuota)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Validar si la cuota ya fue pagada
                if ($cuota->estado_cuota === 'pagada') {
                    return [
                        'error'   => true,
                        'status'  => 400,
                        'message' => 'Esta cuota ya se encuentra totalmente cancelada.'
                    ];
                }
                // 3. Registrar el Pago con los nombres correctos de la tabla pagos
                $pago = Pago::create([
                    'id_cuota'            => $cuota->id_cuota,
                    'monto_pagado'        => $request->monto_pagado,
                    'fecha_pago'          => Carbon::now(),
                    'metodo_pago'         => $request->metodo_pago,
                    'referencia_bancaria' => $request->referencia_bancaria,
                    'banco_origen'        => $request->banco_origen,
                    'estado_pago'         => 'completado',
                    'observaciones'       => $request->observaciones,
                ]);

                // 4. Actualizar el estado de la cuota
                $cuota->estado_cuota = 'pagada';
                $cuota->save();

// 5. Emitir Factura ligada al id_pago
$factura = $pago->factura()->create([
    'numero_control'   => 'FAC-' . time(),
    'fecha_emision'    => Carbon::now(),
    'subtotal'         => $request->monto_pagado,
    'iva'              => 0.00,
    'total_facturado'  => $request->monto_pagado, // <--- Añade esto para completar los campos obligatorios
]);
                // 6. Recalcular el estado del préstamo financiero asociado
                $prestamo = PrestamoFinanciero::with('cuotas')
                    ->where('id_prestamo', $cuota->id_prestamo)
                    ->lockForUpdate()
                    ->firstOrFail();

                $totalCuotas   = $prestamo->cuotas->count();
                $cuotasPagadas = $prestamo->cuotas->where('estado_cuota', 'pagada')->count();
                $cuotasEnMora  = $prestamo->cuotas->where('estado_cuota', 'en_mora')->count();

                if ($cuotasPagadas === $totalCuotas) {
                    $prestamo->estado_prestamo = 'finalizado';
                } elseif ($cuotasEnMora > 0) {
                    $prestamo->estado_prestamo = 'en_mora';
                } else {
                    $prestamo->estado_prestamo = 'activo';
                }

                $prestamo->save();

                return [
                    'error'    => false,
                    'status'   => 201,
                    'pago'     => $pago,
                    'cuota'    => new CuotaResource($cuota),
                    'factura'  => $factura,
                    'prestamo' => [
                        'id_prestamo'  => $prestamo->id_prestamo,
                        'nuevo_estado' => $prestamo->estado_prestamo
                    ]
                ];
            });

            if ($resultado['error']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message']
                ], $resultado['status']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado con éxito y factura emitida.',
                'data'    => [
                    'pago'              => $resultado['pago'],
                    'cuota_actualizada' => $resultado['cuota'],
                    'factura'           => $resultado['factura'],
                    'prestamo'          => $resultado['prestamo']
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago transaccional',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}