<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Capítulo I: Generar comprobante de préstamo en formato XML (XMLELEMENT / XMLFOREST)
     */
    public function exportarPrestamoXml($id)
    {
        // Consulta SQL utilizando funciones XML nativas de PostgreSQL
        $resultado = DB::select("
            SELECT XMLELEMENT(NAME \"ComprobantePrestamo\",
                        XMLFOREST(
                            p.id AS \"IdPrestamo\",
                            p.created_at AS \"FechaRegistro\",
                            COALESCE(SUM(cu.monto_cuota), 0) AS \"MontoTotalCuotas\"
                        )
                   ) AS xml_output
            FROM prestamos p
            LEFT JOIN cuotas cu ON cu.id_prestamo = p.id
            WHERE p.id = ?
            GROUP BY p.id, p.created_at;
        ", [$id]);

        if (empty($resultado)) {
            return response()->json(['error' => 'Préstamo no encontrado'], 404);
        }

        $xmlContenido = $resultado[0]->xml_output;

        // Retornar el archivo XML como descarga directa para el usuario
        return response($xmlContenido, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="comprobante_prestamo_' . $id . '.xml"',
        ]);
    }

    /**
     * Capítulo II: Inferencia analítica de riesgo de morosidad
     */
    public function evaluarRiesgoMorosidad()
    {
        // Consulta con CTE (WITH) y tramos de riesgo basada en la lógica ECA / reglas implementadas
        $reporteRiesgo = DB::select("
            WITH calculo_moras AS (
                SELECT 
                    p.id AS prestamo_id,
                    cu.id_cuota AS cuota_id,
                    cu.fecha_vencimiento,
                    CURRENT_DATE AS fecha_actual,
                    (CURRENT_DATE - cu.fecha_vencimiento) AS dias_transcurridos_vencido,
                    cu.estado_cuota AS estado_cuota
                FROM prestamos p
                JOIN cuotas cu ON cu.id_prestamo = p.id
                WHERE cu.estado_cuota = 'PENDIENTE' 
                  AND cu.fecha_vencimiento < CURRENT_DATE
            )
            SELECT 
                prestamo_id,
                COUNT(cuota_id) AS total_cuotas_vencidas,
                MAX(dias_transcurridos_vencido) AS maximo_dias_mora,
                CASE 
                    WHEN MAX(dias_transcurridos_vencido) > 30 THEN 'ALTO RIESGO / MORA CRITICA'
                    WHEN MAX(dias_transcurridos_vencido) > 15 THEN 'MORA MODERADA'
                    ELSE 'MORA TEMPRANA'
                END AS nivel_inferido_riesgo
            FROM calculo_moras
            GROUP BY prestamo_id;
        ");

        // Retorna los datos en formato JSON para que la interfaz o panel administrativo los consuma
        return response()->json([
            'status' => 'success',
            'data' => $reporteRiesgo
        ]);
    }
}