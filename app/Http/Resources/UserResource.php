<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' =>$this->email,
            'password' => $this->password,
            'partidas_jugadas' => $this->partidas_jugadas,
            'partidas_ganadas' => $this->partidas_ganadas,
            'partidas_empatadas' => $this->partidas_empatadas,
            'partidas_perdidas' => $this->partidas_perdidas,
        ];
    }
}
