<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends BaseModel implements HasMedia {

    use InteractsWithMedia;

    protected $fillable = ['key', 'value'];

    /**
     * Register media collections for site settings
     */
    public function registerMediaCollections(): void {
        // Logo collection
        $this->addMediaCollection('logo')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        // Favicon collection
        $this->addMediaCollection('fav_icon')
                ->singleFile()
                ->acceptsMimeTypes(['image/x-icon', 'image/png', 'image/gif', 'image/jpeg', 'image/jpg', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        // Default user image collection
        $this->addMediaCollection('default_user')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/users/default.png'))
                ->useFallbackPath(public_path('storage/images/users/default.png'));

        // Intro loader collection
        $this->addMediaCollection('intro_loader')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        // Intro logo collection
        $this->addMediaCollection('intro_logo')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        // About images collection
        $this->addMediaCollection('about_image_1')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        $this->addMediaCollection('about_image_2')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        // Login background collection
        $this->addMediaCollection('login_background')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        // Profile cover collection
        $this->addMediaCollection('profile_cover')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/default.png'))
                ->useFallbackPath(public_path('storage/images/default.png'));

        // No data image collection
        $this->addMediaCollection('no_data')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl(asset('storage/images/no_data.png'))
                ->useFallbackPath(public_path('storage/images/no_data.png'));
    }

    /**
     * Register media conversions for site settings
     */
    public function registerMediaConversions($media = null): void {
        $this->addMediaConversion('thumb')
                ->width(150)
                ->height(150)
                ->nonQueued();

        $this->addMediaConversion('medium')
                ->width(500)
                ->height(500)
                ->nonQueued();
    }

    /**
     * Get media URL for a specific collection
     */
    public function getMediaUrl($collection) {
        return $this->getFirstMediaUrl($collection);
    }

    /**
     * Get media URL with conversion
     */
    public function getMediaUrlWithConversion($collection, $conversion = 'thumb') {
        return $this->getFirstMediaUrl($collection, $conversion);
    }

    /**
     * Check if a file can be accepted into a collection
     */
    public function canAcceptFile($file, $collectionName) {
        if (!$file || !is_file($file)) {
            return false;
        }

        // Define accepted MIME types for each collection
        $acceptedMimeTypes = match ($collectionName) {
            'fav_icon' => ['image/x-icon', 'image/png', 'image/gif', 'image/jpeg', 'image/jpg', 'image/webp', 'image/svg+xml'],
            'logo', 'default_user', 'intro_loader', 'intro_logo', 'about_image_1', 'about_image_2', 'login_background', 'profile_cover', 'no_data' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            default => []
        };

        // Check MIME type
        if (!empty($acceptedMimeTypes) && !in_array($file->getMimeType(), $acceptedMimeTypes)) {
            return false;
        }

        // Check file size (if there's a limit set)
        $maxFileSize = config('media-library.max_file_size');
        if ($maxFileSize && $file->getSize() > $maxFileSize) {
            return false;
        }

        return true;
    }
}
