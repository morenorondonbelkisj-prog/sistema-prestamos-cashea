<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrestamoResource extends JsonResource
{
    /**
     * Transformar el recurso de préstamo en un arreglo JSON estructurado.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id_prestamo,
            'monto_total'      => (float) $this->monto_total,
            'cuota_inicial'    => (float) $this->cuota_inicial,
            'monto_financiado' => (float) $this->monto_financiado,
            'numero_cuotas'    => $this->numero_cuotas,
            'estado'           => $this->estado_prestamo,
            'fecha_solicitud'  => $this->fecha_solicitud ? $this->fecha_solicitud->format('Y-m-d') : null,
            'cliente'          => [
                'id'     => $this->cliente->id ?? null,
                'nombre' => $this->cliente->name ?? null,
                'email'  => $this->cliente->email ?? null,
            ],
            'cuotas'           => CuotaResource::collection($this->whenLoaded('cuotas')),
            'creado_el'        => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}