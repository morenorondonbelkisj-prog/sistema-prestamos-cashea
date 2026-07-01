<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABLA BASE: personas (Regla A del documento)
        Schema::create('personas', function (Blueprint $table) {
            $table->id('id_persona'); // PK autoincremental (Serial en Postgres)
            $table->string('cedula')->unique();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('telefono');
            $table->timestamps();
        });

        // 2. TABLA HIJA: clientes (Hereda de personas)
        Schema::create('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cliente')->primary(); // PK y FK al mismo tiempo
            $table->decimal('linea_credito', 14, 2); // Exigencia de la Regla B (Evitar floats)
            $table->integer('score_credito');
            $table->decimal('umbral_alto_costo', 14, 2);
            $table->date('fecha_nacimiento');
            $table->string('estado_cuenta');
            $table->timestamps();

            // Relación de herencia física mediante clave foránea en cascada
            $table->foreign('id_cliente')->references('id_persona')->on('personas')->onDelete('cascade');
        });

        // 3. TABLA HIJA: empleados (Hereda de personas)
        Schema::create('empleados', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empleado')->primary();
            $table->string('codigo_empleado')->unique();
            $table->string('rol');
            $table->date('fecha_ingreso');
            $table->string('estado');
            $table->timestamps();

            $table->foreign('id_empleado')->references('id_persona')->on('personas')->onDelete('cascade');
        });

        // 4. TABLA HIJA: fiadores (Hereda de personas)
        Schema::create('fiadores', function (Blueprint $table) {
            $table->unsignedBigInteger('id_fiador')->primary();
            $table->decimal('ingresos_comprobados', 14, 2);
            $table->timestamps();

            $table->foreign('id_fiador')->references('id_persona')->on('personas')->onDelete('cascade');
        });

        // 5. TABLA CATÁLOGO: comercios_aliados (Regla D del documento)
        Schema::create('comercios_aliados', function (Blueprint $table) {
            $table->id('id_comercio');
            $table->string('rif')->unique();
            $table->string('razon_social');
            $table->string('categoria');
            $table->decimal('porcentaje_comision', 5, 2); // Ej: 05.50 %
            $table->string('estado_afiliacion');
            $table->date('fecha_afiliacion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Se borran en orden inverso para no romper las restricciones de llaves foráneas
        Schema::dropIfExists('comercios_aliados');
        Schema::dropIfExists('fiadores');
        Schema::dropIfExists('empleados');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('personas');
    }
};