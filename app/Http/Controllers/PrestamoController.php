<?php

namespace App\Http\Controllers;

use App\Models\PrestamoFinanciero;
use App\Models\Cuota; // Asegúrate de que esta línea esté escrita
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrestamoController extends Controller
{
    // Método para registrar un nuevo préstamo y sus cuotas quincenales
    public function store(Request $request)
    {
        // 1. Validar los datos de entrada
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'id_comercio' => 'required|exists:comercios_aliados,id_comercio',
            'monto_total' => 'required|numeric|min:1',
        ]);

        // Usamos una transacción de Base de Datos para asegurar que se cree el préstamo Y las cuotas juntas
        DB::beginTransaction();

        try {
            $montoTotal = $request->monto_total;
            
            // Lógica de negocio: Inicial del 40% (típica de Cashea en nivel inicial) y 60% financiado
            $cuotaInicial = $montoTotal * 0.40;
            $montoFinanciado = $montoTotal - $cuotaInicial;
            $numeroCuotas = 4;
            $montoPorCuota = $montoFinanciado / $numeroCuotas;

            // 2. Crear el encabezado del Préstamo
            $prestamo = PrestamoFinanciero::create([
                'id_cliente' => $request->id_cliente,
                'id_comercio' => $request->id_comercio,
                'monto_total' => $montoTotal,
                'cuota_inicial' => $cuotaInicial,
                'monto_financiado' => $montoFinanciado,
                'numero_cuotas' => $numeroCuotas,
                'estado_prestamo' => 'APROBADO',
                'fecha_solicitud' => Carbon::now()->toDateString(),
            ]);

            // 3. Crear las 4 cuotas quincenales (Cada 15 días)
            for ($i = 1; $i <= $numeroCuotas; $i++) {
                Cuota::create([
                    'id_prestamo' => $prestamo->id_prestamo,
                    'numero_cuota' => $i,
                    'monto_cuota' => $montoPorCuota,
                    'monto_penalizacion' => 0.00,
                    'fecha_vencimiento' => Carbon::now()->addDays($i * 15)->toDateString(),
                    'estado_cuota' => 'PENDIENTE',
                ]);
            }

            DB::commit(); // Confirmar cambios en PostgreSQL

            return response()->json([
                'mensaje' => 'Préstamo y cronograma de 4 cuotas generado con éxito',
                'prestamo' => $prestamo->load('cuotas')
            ], 210);

        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, deshace todo en Postgres para evitar datos corruptos
            return response()->json([
                'error' => 'Error al procesar el préstamo',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}