<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Cuota;
use App\Models\PrestamoFinanciero;
use App\Models\FacturaPago; // Importamos el nuevo modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_cuota' => 'required|exists:cuotas,id_cuota',
            'monto_pagado' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'referencia_bancaria' => 'required|string|unique:pagos,referencia_bancaria',
        ]);

        DB::beginTransaction();

        try {
            $cuota = Cuota::findOrFail($request->id_cuota);

            if ($cuota->estado_cuota === 'PAGADA') {
                return response()->json([
                    'error' => 'La cuota seleccionada ya se encuentra pagada.'
                ], 400);
            }

            // 1. Registrar el Pago
            $pago = Pago::create([
                'id_cuota' => $request->id_cuota,
                'monto_pagado' => $request->monto_pagado,
                'fecha_pago' => Carbon::now()->toDateString(),
                'metodo_pago' => $request->metodo_pago,
                'referencia_bancaria' => $request->referencia_bancaria,
            ]);

            // 2. LÓGICA DE FACTURACIÓN AUTOMÁTICA
            $totalFacturado = $request->monto_pagado;
            $divisorIva = 1.16; // Suponiendo IVA del 16%
            $subtotal = round($totalFacturado / $divisorIva, 2);
            $iva = round($totalFacturado - $subtotal, 2);

            // Generar un número de control único incremental básico
            $ultimoId = FacturaPago::max('id_factura') ?? 0;
            $numeroControl = 'FAC-' . Carbon::now()->format('Ymd') . '-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);

            $factura = FacturaPago::create([
                'id_pago' => $pago->id_pago,
                'numero_control' => $numeroControl,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total_facturado' => $totalFacturado,
                'fecha_emision' => Carbon::now()->toDateString(),
            ]);

            // 3. Actualizar la Cuota
            $cuota->update(['estado_cuota' => 'PAGADA']);

            // 4. Verificar liquidación del préstamo completo
            $id_prestamo = $cuota->id_prestamo;
            $cuotasPendientes = Cuota::where('id_prestamo', $id_prestamo)
                                     ->where('estado_cuota', '!=', 'PAGADA')
                                     ->count();

            $prestamo = PrestamoFinanciero::find($id_prestamo);

            if ($cuotasPendientes === 0) {
                $prestamo->update(['estado_prestamo' => 'LIQUIDADO']);
            }

            DB::commit();

            return response()->json([
                'mensaje' => 'Pago y Factura procesados con éxito',
                'pago' => $pago,
                'factura' => $factura,
                'estado_cuota' => 'PAGADA',
                'estado_prestamo_actual' => $prestamo->estado_prestamo
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al procesar el pago y la facturación',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}