<?php

namespace App\Models;

// Modelo Eloquent estándar de Laravel (PostgreSQL / Relacional)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoPrestamo extends Model
{
    use HasFactory;

    // Conexión explícita a PostgreSQL
    protected $connection = 'pgsql'; 

    // Nombre de la tabla en PostgreSQL (reemplaza por el nombre real de tu tabla)
    protected $table = 'contratos_prestamos'; 

    // Desactivar protección de asignación masiva
    protected $guarded = [];

    // Castings necesarios para JSONB y fechas en PostgreSQL
    protected $casts = [
        'esquema_pagos_json' => 'array',
        'condiciones'        => 'array',
        'creado_el'          => 'datetime',
    ];

    // Relación inversa con el Préstamo
    public function prestamo()
    {
        return $this->belongsTo(PrestamoFinanciero::class, 'prestamo_id');
    }
}