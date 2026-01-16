<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;
use Spatie\MediaLibrary\InteractsWithMedia;

trait HasAutoMedia
{
    use InteractsWithMedia, CompressesImages;

    public static function bootHasAutoMedia(): void
    {
        static::saved(function ($model) {
            if (method_exists($model, 'autoUploadMediaFromRequest')) {
                $model->autoUploadMediaFromRequest();
            }
        });
    }

    // If your model overrides registerMediaCollections(), call $this->registerAutoMediaCollections() inside it.
    public function registerMediaCollections(): void
    {
        $this->registerAutoMediaCollections();

        if (method_exists($this, 'extraRegisterMediaCollections')) {
            $this->extraRegisterMediaCollections();
        }
    }

    public function registerAutoMediaCollections(): void
    {
        foreach ($this->normalizeMediaMap($this->getAutoMediaMap()) as $cfg) {
            $mc = $this->addMediaCollection($cfg['collection']);
            if (!$cfg['multiple']) {
                $mc->singleFile();
            }
        }
    }

    public function autoUploadMediaFromRequest(): void
    {
        $request = request();
        if (!$request || !$request->method()) {
            return;
        }

        foreach ($this->normalizeMediaMap($this->getAutoMediaMap()) as $cfg) {
            $field            = $cfg['field'];
            $collection       = $cfg['collection'];
            $multiple         = $cfg['multiple'];
            $clearOnUpdate    = $cfg['clear_on_update'];
            $preserveOriginal = $cfg['preserve_original'];

            if ($multiple) {
                $files = $request->file($field);
                if (is_array($files) && count($files)) {
                    if ($clearOnUpdate) {
                        $this->clearMediaCollection($collection);
                    }
                    foreach ($files as $file) {
                        if ($file instanceof UploadedFile) {
                            // Use compression for images
                            $this->addMediaWithCompression($file, $collection, $preserveOriginal);
                        }
                    }
                }
            } else {
                if ($request->hasFile($field)) {
                    if ($clearOnUpdate) {
                        $this->clearMediaCollection($collection);
                    }
                    $file = $request->file($field);
                    // Use compression for images
                    $this->addMediaWithCompression($file, $collection, $preserveOriginal);
                } elseif ($request->boolean("{$field}_remove")) {
                    $this->clearMediaCollection($collection);
                }
            }
        }
    }

    protected function getAutoMediaMap(): array
    {
        // Prefer a model method if provided
        if (method_exists($this, 'autoMedia')) {
            return (array) $this->autoMedia();
        }

        // Or a property defined on the model
        return property_exists($this, 'autoMedia') ? (array) $this->autoMedia : [];
    }

    protected function normalizeMediaMap(array $map): array
    {
        $normalized = [];

        foreach ($map as $key => $value) {
            if (is_int($key)) {
                $field = (string) $value;
                $normalized[] = [
                    'field'             => $field,
                    'collection'        => $field,
                    'multiple'          => false,
                    'clear_on_update'   => true,
                    'preserve_original' => false,
                ];
                continue;
            }

            $field = (string) $key;

            if (is_string($value)) {
                $normalized[] = [
                    'field'             => $field,
                    'collection'        => $value,
                    'multiple'          => false,
                    'clear_on_update'   => true,
                    'preserve_original' => false,
                ];
                continue;
            }

            if (is_array($value)) {
                $normalized[] = [
                    'field'             => $field,
                    'collection'        => (string) ($value['collection'] ?? $field),
                    'multiple'          => (bool) ($value['multiple'] ?? false),
                    'clear_on_update'   => (bool) ($value['clear_on_update'] ?? !($value['multiple'] ?? false)),
                    'preserve_original' => (bool) ($value['preserve_original'] ?? false),
                ];
            }
        }

        return $normalized;
    }
}