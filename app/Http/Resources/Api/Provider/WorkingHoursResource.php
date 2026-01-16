<?php

namespace App\Http\Resources\Api\Provider;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHoursResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'day' => __('admin.' . $this->day),
            'day_enum' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_working' => $this->is_working,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
