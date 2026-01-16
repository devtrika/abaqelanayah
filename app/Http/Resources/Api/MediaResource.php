<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Media Resource for handling file information
 *
 * Response structure:
 * {
 *   "id": 1,
 *   "url": "https://example.com/storage/media/file.jpg",
 *   "mime_type": "image/jpeg",
 *   "size": 1024000,
 *   "file_name": "file.jpg",
 *   "collection_name": "logo"
 * }
 */
class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->getUrl(),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'file_name' => $this->file_name,
            'collection_name' => $this->collection_name,
        ];
    }
}
