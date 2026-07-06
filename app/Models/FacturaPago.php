<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaPago extends Model
{
    use HasFactory;

    protected $table = 'facturas_pago';
    protected $primaryKey = 'id_factura'; //llave primaria

    protected $fillable = [
        'id_pago',
        'numero_control',
        'subtotal',
        'iva',
        'total_facturado',
        'fecha_emision'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total_facturado' => 'decimal:2',
    ];

    // Relación Una factura pertenece a un pago único.
     
    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'id_pago', 'id_pago');
    }
}