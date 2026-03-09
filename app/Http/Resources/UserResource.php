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
        return [
            "id" => $this->id,
            "student_code" => $this->student_code,
            "full_name" => $this->full_name,
            "email" => $this->email,
            "role" => $this->role,
            "career_name" => $this->whenLoaded('career', fn() => $this->career->name),
            "mencion_name" => $this->whenLoaded('specialization', fn() => $this->specialization->name)
        ];
    }
}
