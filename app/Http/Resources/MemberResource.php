<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'role' =>$this->role,
            'desc' => $this->desc,
            'photo' => $this->photo,
            'email'=> $this->email,
            'website' => $this->website,
            'linkedin' => $this->linkedin,
            'dribbble' => $this->dribbble
        ];
    }
}
