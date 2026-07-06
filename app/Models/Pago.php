<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // Importar la  relación

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';  // Llave primaria

    protected $fillable = [
        'id_cuota',          // Relación directa con la cuota
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

    
     // Relación: Un pago pertenece a una cuota específica.
     
    public function cuota(): BelongsTo
    {
        return $this->belongsTo(Cuota::class, 'id_cuota', 'id_cuota');
    }

    
     // Relación: Un pago genera una única factura fiscal (1:1).
     
    public function factura(): HasOne
    {
        // La foránea en 'factura_pagos' es 'pago_id' y la local aquí es 'id_pago'
        return $this->hasOne(FacturaPago::class, 'pago_id', 'id_pago');
    }
}