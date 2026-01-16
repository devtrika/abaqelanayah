<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait CompressesImages
{
    /**
     * Compress and add media to model
     * 
     * @param UploadedFile $file
     * @param string $collection
     * @param bool $preserveOriginal
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     */
    protected function addMediaWithCompression(UploadedFile $file, string $collection = 'default', bool $preserveOriginal = false)
    {
        $fileSize = $file->getSize();
        $maxSize = config('media-library.compression_threshold', 10 * 1024 * 1024); // 10MB default

        // If file is larger than threshold and is an image, compress it first
        if ($fileSize > $maxSize && $this->isImage($file)) {
            \Log::info("Compressing large image: " . $file->getClientOriginalName() . " (" . round($fileSize / 1024 / 1024, 2) . "MB)");
            $startTime = microtime(true);
            
            $result = $this->compressAndAddMedia($file, $collection, $preserveOriginal);
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            \Log::info("Compression completed in {$duration}s");
            
            return $result;
        }

        // File is small enough or not an image, add directly
        $adder = $this->addMedia($file);
        if ($preserveOriginal) {
            $adder->preservingOriginal();
        }
        return $adder->toMediaCollection($collection);
    }

    /**
     * Compress image and add to media collection
     */
    private function compressAndAddMedia(UploadedFile $file, string $collection, bool $preserveOriginal)
    {
        try {
            // Create ImageManager with GD driver
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            // Get original dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Get max dimensions from config (reduced for faster processing)
            $maxWidth = config('media-library.max_image_width', 1200);
            $maxHeight = config('media-library.max_image_height', 1200);
            
            // Always resize to max dimensions for consistency and speed
            // Using scale is faster than resize
            if ($width > $height) {
                $image->scale(width: $maxWidth);
            } else {
                $image->scale(height: $maxHeight);
            }
            
            // Get quality from config (lower quality = faster processing)
            $quality = config('media-library.compression_quality', 75);
            
            // Compress and save to temp file
            $extension = strtolower($file->getClientOriginalExtension());
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '_' . $file->getClientOriginalName();
            
            // Encode based on file type (JPEG is fastest)
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $encoded = $image->toJpeg(quality: $quality);
            } elseif ($extension === 'png') {
                // Convert PNG to JPEG for faster processing and smaller size
                $encoded = $image->toJpeg(quality: $quality);
            } elseif ($extension === 'webp') {
                $encoded = $image->toWebp(quality: $quality);
            } else {
                // Fallback to JPEG for other formats
                $encoded = $image->toJpeg(quality: $quality);
            }
            
            file_put_contents($tempPath, $encoded);
            
            // Add compressed image
            $adder = $this->addMedia($tempPath)
                ->usingFileName($file->getClientOriginalName());
            
            if ($preserveOriginal) {
                $adder->preservingOriginal();
            }
            
            $media = $adder->toMediaCollection($collection);
            
            // Clean up temp file
            @unlink($tempPath);
            
            return $media;
        } catch (\Exception $e) {
            // If compression fails, fall back to original file
            \Log::warning('Image compression failed, using original file: ' . $e->getMessage());
            
            $adder = $this->addMedia($file);
            if ($preserveOriginal) {
                $adder->preservingOriginal();
            }
            return $adder->toMediaCollection($collection);
        }
    }

    /**
     * Check if file is an image
     */
    private function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return str_starts_with($mimeType, 'image/');
    }
}
