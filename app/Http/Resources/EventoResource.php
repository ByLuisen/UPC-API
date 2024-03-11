<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nombre' =>$this->nombre,
            'tipo' => $this->tipo,
            'numero_participantes' => $this->numero_participantes,
            'fecha_inicio' => $this->fecha_inicio,
            'duracion' => $this->duracion
        ];
    }
}
