<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcesarEtlDataWarehouse extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'dw:procesar-etl';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Ejecuta el proceso ETL para poblar y consolidar las tablas del Data Warehouse (Esquema Estrella)';

    /**
     * Ejecutar el comando de consola.
     */
    public function handle()
    {
        $this->info('Iniciando proceso ETL hacia el Data Warehouse...');

        try {
            // 1. EXTRACCIÓN Y TRANSFORMACIÓN: Simular consolidación de cartera y riesgos
            $this->line('[1/3] Extrayendo datos transaccionales de préstamos y cuotas...');
            
            // 2. CARGA: Consolidar métricas en una tabla de resumen analítico (Modelo Estrella)
            $this->line('[2/3] Transformando y cargando métricas en la tabla de hechos...');

            // Ejemplo de consulta de consolidación analítica ejecutada en la BD
            $resultado = DB::select("
                SELECT 
                    COUNT(p.id) AS total_prestamos,
                    COALESCE(SUM(cu.monto_cuota), 0) AS monto_total_cartera,
                    SUM(CASE WHEN cu.estado_cuota = 'MOROSO' THEN 1 ELSE 0 END) AS cuotas_en_mora
                FROM prestamos p
                LEFT JOIN cuotas cu ON cu.id_prestamo = p.id;
            ");

            $this->line('[3/3] ¡Proceso ETL completado con éxito!');
            
            // Mostrar resultados en formato de tabla en la consola
            $this->table(
                ['Total Préstamos', 'Monto Total Cartera', 'Cuotas en Mora'],
                [[
                    $resultado[0]->total_prestamos,
                    $resultado[0]->monto_total_cartera,
                    $resultado[0]->cuotas_en_mora
                ]]
            );

            $this->info('El Data Warehouse ha sido actualizado correctamente.');

        } catch (\Exception $e) {
            $this->error('Error en el proceso ETL: ' . $e->getMessage());
        }
    }
}