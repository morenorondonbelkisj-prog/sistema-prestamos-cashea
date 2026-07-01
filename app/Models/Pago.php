<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_cuota',          // <-- Cambiado de id_prestamo a id_cuota para que Eloquent no lo ignore
        'monto_pagado',
        'fecha_pago',
        'metodo_pago',
        'referencia_bancaria',
        'banco_origen',
        'estado_pago',
        'observaciones'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto_pagado' => 'decimal:2',
    ];

    /**
     * Relación: Un pago pertenece a una cuota específica.
     */
    public function cuota(): BelongsTo
    {
        return $this->belongsTo(Cuota::class, 'id_cuota', 'id_cuota');
    }
}