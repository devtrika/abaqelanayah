<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHoursResource extends JsonResource
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
            'day' => __('admin.' . $this->day),
            'day_enum' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_working' => $this->is_working,

        ];
    }
}
