<?php

namespace App\Http\Resources\Gestor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "code" => $this->code,
            "name" => $this->name,
            "uc" => $this->uc,
            "semester" => $this->semester,
            "prelacion_text" => $this->prelacion_text,
            "specialization_id" => $this->specialization_id,
            "type" => $this->type
        ];
    }
}
