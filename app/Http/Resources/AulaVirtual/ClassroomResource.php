<?php

namespace App\Http\Resources\AulaVirtual;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomResource extends JsonResource
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
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'cover_path' => $this->cover_path,
            'access_code' => $this->when($request->user()->can('update',$this->resource),$this->access_code),
            'professor' => new UserResource($this->whenLoaded('professor')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            'members_count' => $this->whenCounted('members'),
            'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),
            'assignments_count' => $this->whenCounted('assigments'),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'posts_count' => $this->whenCounted('posts')
        ];
    }
}
