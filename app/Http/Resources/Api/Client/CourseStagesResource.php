<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseStagesResource extends JsonResource
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
            'name' => $this->title,
            'media' => MediaResource::make($this->getFirstMedia('stage-videos')),
            'is_completed' => (bool)$this->userCompletion?->completed_at,
            'last_watch_time' => $this->userCompletion?->last_watch_time
        ];
    }
}
