<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear la función en PL/pgSQL
        DB::unprepared("
            CREATE OR REPLACE FUNCTION verificar_morosidad_pago()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.fecha_vencimiento < CURRENT_DATE AND NEW.estado_cuota = 'PENDIENTE' THEN
                    NEW.estado_cuota := 'MOROSO';
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // 2. Crear el Trigger asociado a la tabla cuotas
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_evaluar_pago_tardio ON cuotas;
            CREATE TRIGGER trg_evaluar_pago_tardio
            BEFORE UPDATE ON cuotas
            FOR EACH ROW
            EXECUTE FUNCTION verificar_morosidad_pago();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_evaluar_pago_tardio ON cuotas;");
        DB::unprepared("DROP FUNCTION IF EXISTS verificar_morosidad_pago();");
    }
};