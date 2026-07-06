<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrestamoFinanciero; // Ajusta al nombre exacto de tu modelo si varía
use App\Models\Cuota;              // Ajusta al nombre exacto de tu modelo si varía
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PrestamoApiController extends Controller
{
    /**
     * EVIDENCIA: API REST - Listar todos los préstamos con sus cuotas (Relaciones ORM)
     */
    public function index()
    {
        // Usamos Eager Loading ('with') para traer los préstamos junto a sus cuotas asociadas
        $prestamos = PrestamoFinanciero::with('cuotas')->get();

        return response()->json([
            'success' => true,
            'data' => $prestamos
        ], 200);
    }

    /**
     * EVIDENCIA: API REST & Relaciones ORM - Solicitar Préstamo y Generar Cuotas
     */
   public function store(Request $request)
    {
        // 1. Validamos usando tus campos reales (id_cliente, numero_cuotas, etc.)
        $validator = Validator::make($request->all(), [
            'id_cliente'      => 'required|exists:users,id', // Verifica que el cliente exista en users
            'monto_total'     => 'required|numeric|min:1',
            'numero_cuotas'   => 'required|integer|min:1|max:12', 
            'cuota_inicial'   => 'required|numeric|min:0',
            'monto_financiado'=> 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 3. Crear el encabezado del Préstamo usando tus columnas reales de PostgreSQL
            $prestamo = PrestamoFinanciero::create([
                'id_cliente'       => $request->id_cliente,
                'id_comercio'      => $request->id_comercio ?? 1, // Por si manejas comercios asignados
                'monto_total'      => $request->monto_total,
                'cuota_inicial'    => $request->cuota_inicial,
                'monto_financiado' => $request->monto_financiado,
                'numero_cuotas'    => $request->numero_cuotas,
                'estado_prestamo'  => 'pendiente', 
                'fecha_solicitud'  => Carbon::now()->toDateString(),
            ]);

            // 4. Generación automática de cuotas (se mantiene igual usando la relación)
            $montoCuota = round($request->monto_financiado / $request->numero_cuotas, 2);
            $fechaVencimiento = Carbon::now();

            for ($i = 1; $i <= $request->numero_cuotas; $i++) {
                $fechaVencimiento->addDays(15);

                $prestamo->cuotas()->create([
                    'numero_numero_cuota' => $i,
                    'monto'               => $montoCuota,
                    'estado'              => 'pendiente',
                    'fecha_vencimiento'   => $fechaVencimiento->toDateString(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Préstamo aprobado y cuotas generadas correctamente',
                'data' => $prestamo->load('cuotas')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el préstamo transaccional',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * EVIDENCIA: API REST - Consultar un préstamo específico con sus cuotas
     */
    public function show(string $id)
    {
        $prestamo = PrestamoFinanciero::with('cuotas')->find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $prestamo
        ], 200);
    }
}