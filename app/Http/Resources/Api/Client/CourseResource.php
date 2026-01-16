<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'logo' => $this->image && is_object($this->image)
                ? MediaResource::make($this->image)
                : null,            
            'name' => $this->name,
            'instructor_name' => $this->instructor_name,
            'duration' => $this->duration,
            'vedios_count' => $this->stages_count,
            'description' => $this->description,
            'price' => $this->price,
            'stages' => CourseStagesResource::collection($this->whenLoaded('stages')),

            // User ownership and enrollment attributes
            'is_owned_by_user' => $this->is_owned_by_user,
            'can_access' => $this->can_access,
            'user_progress' => $this->user_progress,
            'is_completed' => $this->enrollment?->status,
            // Legacy enrollment attributes (for backward compatibility)
            'is_enrolled' => $this->when(isset($this->is_enrolled), $this->is_enrolled),
          
        ];
    }
}
