<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\Product;
use App\Models\Service;

class RateableTypeDetector
{
    /**
     * Mapping of rateable types to their model classes
     */
    private const RATEABLE_MODELS = [
        'provider' => Provider::class,
        'product' => Product::class,
        'service' => Service::class,
    ];

    /**
     * Detect the rateable type by checking which model the ID belongs to
     *
     * @param int $rateableId
     * @return string|null
     */
    public static function detectType(int $rateableId): ?string
    {
        foreach (self::RATEABLE_MODELS as $type => $modelClass) {
            if ($modelClass::where('id', $rateableId)->exists()) {
                return $type;
            }
        }
        
        return null;
    }

    /**
     * Get the model class for a given rateable type
     *
     * @param string $type
     * @return string|null
     */
    public static function getModelClass(string $type): ?string
    {
        return self::RATEABLE_MODELS[$type] ?? null;
    }

    /**
     * Get the model instance for a given type and ID
     *
     * @param string $type
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function getModel(string $type, int $id): ?\Illuminate\Database\Eloquent\Model
    {
        $modelClass = self::getModelClass($type);
        
        if (!$modelClass) {
            return null;
        }
        
        return $modelClass::find($id);
    }

    /**
     * Get all available rateable types
     *
     * @return array
     */
    public static function getAvailableTypes(): array
    {
        return array_keys(self::RATEABLE_MODELS);
    }

    /**
     * Check if a type is valid
     *
     * @param string $type
     * @return bool
     */
    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, self::RATEABLE_MODELS);
    }

    /**
     * Detect type and get model instance in one call
     *
     * @param int $rateableId
     * @return array|null Returns ['type' => string, 'model' => Model] or null
     */
    public static function detectTypeAndModel(int $rateableId): ?array
    {
        foreach (self::RATEABLE_MODELS as $type => $modelClass) {
            $model = $modelClass::find($rateableId);
            if ($model) {
                return [
                    'type' => $type,
                    'model' => $model
                ];
            }
        }
        
        return null;
    }
}
