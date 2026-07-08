<?php

namespace App\Models;

// ¡OJO! Usamos el modelo de MongoDB, no el tradicional de Laravel
use MongoDB\Laravel\Eloquent\Model; 

class ContratoPrestamo extends Model
{
    // Indicamos explícitamente la conexión NoSQL
    protected $connection = 'mongodb'; 
    
    // El nombre de la "colección" (el equivalente a tabla en MongoDB)
    protected $collection = 'contratos_digitales'; 

    // Permitimos guardar cualquier estructura JSON dentro de estos campos
    protected $fillable = [
        'prestamo_id',
        'codigo_contrato',
        'cedula_cliente',
        'terminos_condiciones',
        'esquema_pagos_json', // Aquí irá la matriz de cuotas estructurada
        'firmado_digitalmente',
        'fecha_emision'
    ];

    // Forzamos a que el esquema de pagos se guarde y lea como un arreglo/JSON nativo
    protected $casts = [
       // 'esquema_pagos_json' => 'array',
        'fecha_emision' => 'datetime',
    ];
}