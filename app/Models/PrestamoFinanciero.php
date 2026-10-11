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

    /**
     * Conversión automática de tipos de datos al serializar en JSON.
     */
    protected $casts = [
        'monto_total'      => 'decimal:2',
        'cuota_inicial'    => 'decimal:2',
        'monto_financiado' => 'decimal:2',
        'numero_cuotas'    => 'integer',
        'fecha_solicitud'  => 'date:Y-m-d',
    ];

    /**
     * MUTATOR: Garantiza que el estado siempre se guarde en minúsculas en la BD.
     */
    public function setEstadoPrestamoAttribute($value)
    {
        $this->attributes['estado_prestamo'] = strtolower($value);
    }

    /**
     * ACCESSOR: Garantiza que el estado siempre se devuelva en minúsculas al consultarse.
     */
    public function getEstadoPrestamoAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * RELACIÓN: Un préstamo pertenece a un Usuario/Cliente (Modelo User).
     * Mapea id_cliente (FK en prestamos_financieros) contra id (PK en users).
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id');
    }

    /**
     * RELACIÓN: Un préstamo se divide en muchas cuotas (1 a N).
     * Mapea id_prestamo (PK) contra id_prestamo (FK en cuotas).
     */
    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'id_prestamo', 'id_prestamo')
                    ->orderBy('numero_cuota', 'asc');
    }
}