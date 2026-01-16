<?php

namespace App\Http\Resources\Api\Client;

use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "title"=> $this->title,
            "content"=> $this->content,
            'image' => MediaResource::make($this->getFirstMedia('blogs')),
            'likes_count' => $this->likes_count,
            'dislikes_count' => $this->dislikes_count,
            'comments_count' => $this->comments_count,  
            'is_liked' => $this->is_liked,
            'is_disliked' => $this->is_disliked,
            'comments' => BlogCommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
