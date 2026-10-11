<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FacturaPago; // Asegúrate de importar el correcto

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';
    public $timestamps = false; 

    protected $fillable = [
        'id_cuota',
        'monto_pagado',
        'fecha_pago',
        'metodo_pago',
        'referencia_bancaria',
        'banco_origen',
        'estado_pago',
        'observaciones'
    ];

    // Relación con factura (usando FacturaPago)
    public function factura()
    {
        return $this->hasOne(FacturaPago::class, 'id_pago', 'id_pago');
    }
}