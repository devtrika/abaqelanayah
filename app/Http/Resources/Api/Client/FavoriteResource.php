<?php

namespace App\Http\Resources\Api\Client;

use App\Http\Resources\Api\Client\ProductResource;
use App\Http\Resources\Api\Client\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            'type' => $this->getItemType(),
            'item' => $this->getItemResource(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the item type (product or service)
     */
    private function getItemType(): string
    {
        $type = class_basename($this->favoritable_type);
        return strtolower($type);
    }

    /**
     * Get the appropriate resource for the favoritable item
     */
    private function getItemResource()
    {
        if (!$this->favoritable) {
            return null;
        }

        $itemType = $this->getItemType();

        if ($itemType === 'product') {
            return new ProductResource($this->favoritable);
        } elseif ($itemType === 'service') {
            return new ServiceResource($this->favoritable);
        } elseif ($itemType === 'provider') {
            return new ProviderResource($this->favoritable);
        }

        return null;
    }
}
