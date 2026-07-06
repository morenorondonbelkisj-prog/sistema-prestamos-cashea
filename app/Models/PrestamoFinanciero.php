<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamoFinanciero extends Model
{
    use HasFactory;

    // Indicamos la tabla real en PostgreSQL y su llave primaria
    protected $table = 'prestamos_financieros';
    protected $primaryKey = 'id_prestamo';

    // Columnas que permitiremos rellenar de forma masiva
    protected $fillable = [
        'id_cliente',
        'id_comercio',
        'monto_total',
        'cuota_inicial',
        'monto_financiado',
        'numero_cuotas',
        'estado_prestamo',
        'fecha_solicitud'
    ];

    // RELACIÓN ORIENTADA A OBJETOS Un préstamo pertenece a un Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    // RELACIÓN ORIENTADA A OBJETOS Un préstamo se divide en muchas cuotas (1 a N)
    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'id_prestamo', 'id_prestamo');
    }
}