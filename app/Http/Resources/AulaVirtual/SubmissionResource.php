<?php

namespace App\Http\Resources\AulaVirtual;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'content' => $this->content,
            'file_path' => $this->file_path,
            'submitted_at'=> $this->submitted_at,
            'is_graded'=> $this->is_graded,
            'grade'=> $this->when($this->is_graded == true,$this->grade),
            'teacher_feedback'=>$this->when($this->is_graded == true,$this->teacher_feedback),
            'assignment' => new AssignmentResource($this->whenLoaded('assignment' ,$this->assignment)),
            'student' => new UserResource($this->whenLoaded('student',$this->student))
        ];
    }
}
