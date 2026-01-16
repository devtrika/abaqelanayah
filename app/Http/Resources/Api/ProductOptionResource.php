<?php
namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductOptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => __("apis.option_type_{$this->type}"),
            'additional_price' => $this->additional_price,
            'is_default' => $this->is_default,
        ];
    }

    /**
     * Create a new resource collection grouped by type.
     *
     * @param  mixed  $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collectionGroupedByType($resource)
    {
        $grouped = collect($resource)->groupBy('type')->map(function ($options, $type) {
            return [
                'type' => __("apis.option_type_{$type}"),
                'data' => self::collection($options)
            ];
        })->values();

        return $grouped;
    }
}
