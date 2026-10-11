<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuotaResource extends JsonResource
{
    /**
     * Transformar el recurso de cuota en un arreglo JSON estructurado.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id_cuota,
            'numero_cuota'       => $this->numero_cuota,
            'monto'              => (float) $this->monto_cuota,
            'monto_penalizacion' => (float) $this->monto_penalizacion,
            'monto_total_cuota'  => (float) ($this->monto_cuota + $this->monto_penalizacion),
            'estado'             => $this->estado_cuota,
            'fecha_vencimiento' => $this->fecha_vencimiento ? \Carbon\Carbon::parse($this->fecha_vencimiento)->format('d/m/Y') : null,
        ];
    }
}