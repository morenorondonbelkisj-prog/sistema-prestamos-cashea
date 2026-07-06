<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    use HasFactory;

    protected $table = 'cuotas';
    protected $primaryKey = 'id_cuota'; //llave primaria

    protected $fillable = [
        'id_prestamo',
        'numero_cuota',
        'monto_cuota',
        'monto_penalizacion',
        'fecha_vencimiento',
        'estado_cuota'
    ];

    // RELACIÓN: Una cuota pertenece a un préstamo específico
    public function prestamo()
    {
        return $this->belongsTo(PrestamoFinanciero::class, 'id_prestamo', 'id_prestamo');
    }
}