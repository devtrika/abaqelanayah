<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $media = $this->getFirstMedia('chat-attachments');
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'admin_id' => $this->admin_id,
            'sender_type' => $this->sender_type,
            'message' => $this->message,
            'message_type' => $this->message_type,
            'created_at' => $this->created_at,
            'file' => $media ? [
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'name' => $media->name,
            ] : null,
        ];
    }
} 