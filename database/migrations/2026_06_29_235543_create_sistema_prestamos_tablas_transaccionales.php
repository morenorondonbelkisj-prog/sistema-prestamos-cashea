<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABLA: prestamos_financieros
        Schema::create('prestamos_financieros', function (Blueprint $table) {
            $table->id('id_prestamo');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_comercio');
            $table->decimal('monto_total', 14, 2);
            $table->decimal('cuota_inicial', 14, 2);
            $table->decimal('monto_financiado', 14, 2);
            $table->integer('numero_cuotas')->default(4); // Típico de 4 cuotas quincenales
            $table->string('estado_prestamo'); // APROBADO, LIQUIDADO, MOROSO
            $table->date('fecha_solicitud');
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('restrict');
            $table->foreign('id_comercio')->references('id_comercio')->on('comercios_aliados')->onDelete('restrict');
        });

        // 2. TABLA: cuotas
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id('id_cuota');
            $table->unsignedBigInteger('id_prestamo');
            $table->integer('numero_cuota'); // 1, 2, 3 o 4
            $table->decimal('monto_cuota', 14, 2);
            $table->decimal('monto_penalizacion', 14, 2)->default(0.00);
            $table->date('fecha_vencimiento');
            $table->string('estado_cuota'); // PENDIENTE, PAGADA, VENCIDA
            $table->timestamps();

            $table->foreign('id_prestamo')->references('id_prestamo')->on('prestamos_financieros')->onDelete('cascade');
        });

        // 3. TABLA: pagos
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_cuota');
            $table->decimal('monto_pagado', 14, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago'); // PAGO_MOVIL, TRANSFERENCIA
            $table->string('referencia_bancaria')->unique();
            $table->timestamps();

            $table->foreign('id_cuota')->references('id_cuota')->on('cuotas')->onDelete('restrict');
        });

        // 4. TABLA: facturas_pago (Relación 1:1 estricta con pago mediante UNIQUE)
        Schema::create('facturas_pago', function (Blueprint $table) {
            $table->id('id_factura');
            $table->unsignedBigInteger('id_pago')->unique();
            $table->string('numero_control')->unique();
            $table->decimal('subtotal', 14, 2);
            $table->decimal('iva', 14, 2);
            $table->decimal('total_facturado', 14, 2);
            $table->date('fecha_emision');
            $table->timestamps();

            $table->foreign('id_pago')->references('id_pago')->on('pagos')->onDelete('cascade');
        });

        // 5. TABLA: movimientos_cupo
        Schema::create('movimientos_cupo', function (Blueprint $table) {
            $table->id('id_movimiento');
            $table->unsignedBigInteger('id_cliente');
            $table->string('tipo_movimiento'); // CONSUMO, RESTITUCION, AJUSTE
            $table->decimal('monto', 14, 2);
            $table->decimal('saldo_cupo_resultante', 14, 2);
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_cupo');
        Schema::dropIfExists('facturas_pago');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('cuotas');
        Schema::dropIfExists('prestamos_financieros');
    }
};