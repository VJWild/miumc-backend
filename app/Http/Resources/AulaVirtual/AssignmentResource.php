<?php

namespace App\Http\Resources\AulaVirtual;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'classroom_id' => $this->classroom_id,
            'title' => $this->title,
            'description' => $this->description,
            'enlace_archivo' => $this->file_path,
            'fecha_limite' => $this->due_time,
            'ponderacion' => $this->points,
            'classroom' => new ClassroomResource($this->whenLoaded('classroom',$this->classroom)),
            'submissions' => SubmissionResource::collection($this->whenLoaded('submissions',$this->submissions))
        ];
            
    }
}
