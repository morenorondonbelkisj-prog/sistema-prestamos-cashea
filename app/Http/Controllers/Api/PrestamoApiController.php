<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrestamoFinanciero; 
use App\Models\Cuota;              
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PrestamoApiController extends Controller
{
    /**
     * Resuelve el error GET /api/prestamos
     * Listado paginado de préstamos con sus cuotas y cliente.
     */
    public function index()
    {
        $prestamos = PrestamoFinanciero::with(['cuotas', 'cliente'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $prestamos
        ], 200);
    }

    /**
     * Registro transaccional de préstamo y tabla de amortización.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_cliente'       => 'required|exists:clientes,id_cliente',
            'monto_total'      => 'required|numeric|min:1',
            'numero_cuotas'    => 'required|integer|min:1|max:12', 
            'cuota_inicial'    => 'required|numeric|min:0',
            'monto_financiado' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Validación de consistencia financiera
        if (round($request->monto_total, 2) !== round($request->cuota_inicial + $request->monto_financiado, 2)) {
            return response()->json([
                'success' => false,
                'message' => 'El monto total debe ser igual a la suma de la cuota inicial y el monto financiado.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 1. Crear Préstamo en PostgreSQL
            $prestamo = PrestamoFinanciero::create([
                'id_cliente'       => $request->id_cliente,
                'id_comercio'      => $request->id_comercio ?? 1,
                'monto_total'      => $request->monto_total,
                'cuota_inicial'    => $request->cuota_inicial,
                'monto_financiado' => $request->monto_financiado,
                'numero_cuotas'    => $request->numero_cuotas,
                'frecuencia_pago'  => $request->frecuencia_pago ?? 'quincenal',
                'estado_prestamo'  => 'pendiente', 
                'fecha_solicitud'  => Carbon::now()->toDateString(),
            ]);

            // 2. Cálculo preciso de amortización (sin pérdida de centavos)
            $montoBase = floor(($request->monto_financiado / $request->numero_cuotas) * 100) / 100;
            $diferenciaCentavos = round($request->monto_financiado - ($montoBase * $request->numero_cuotas), 2);

            $fechaBase = Carbon::now();

            for ($i = 1; $i <= $request->numero_cuotas; $i++) {
                // Ajustar última cuota con la diferencia de centavos
                $montoCuota = ($i === (int)$request->numero_cuotas) 
                    ? round($montoBase + $diferenciaCentavos, 2) 
                    : $montoBase;

                // Clonar objeto Carbon para evitar mutación acumulativa
                $fechaVencimiento = $fechaBase->copy()->addDays(15 * $i);

                $prestamo->cuotas()->create([
                    'numero_cuota'       => $i,
                    'monto_cuota'        => $montoCuota,
                    'monto_penalizacion' => 0.00,
                    'estado_cuota'       => 'pendiente',
                    'fecha_vencimiento'  => $fechaVencimiento->toDateString(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Préstamo y cuotas registrados exitosamente en PostgreSQL',
                'data'    => $prestamo->load('cuotas')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el préstamo transaccional',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}