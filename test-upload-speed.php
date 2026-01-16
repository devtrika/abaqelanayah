<?php

/**
 * Test Upload Speed Diagnostic Script
 * Run this via: php test-upload-speed.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "========================================\n";
echo "Upload Speed Diagnostic Test\n";
echo "========================================\n\n";

// Test 1: Check Queue Configuration
echo "1. Checking Queue Configuration...\n";
echo "   QUEUE_CONNECTION: " . config('queue.default') . "\n";
echo "   MEDIA_QUEUE: " . config('media-library.queue_name') . "\n";
echo "   Queue conversions by default: " . (config('media-library.queue_conversions_by_default') ? 'YES' : 'NO') . "\n";
echo "\n";

// Test 2: Check Database Connection
echo "2. Checking Database Connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Database connected\n";
} catch (\Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check Jobs Table
echo "3. Checking Jobs Table...\n";
try {
    $jobsCount = DB::table('jobs')->count();
    echo "   ✅ Jobs table exists\n";
    echo "   Pending jobs: $jobsCount\n";
    
    if ($jobsCount > 0) {
        $jobs = DB::table('jobs')->limit(5)->get();
        foreach ($jobs as $job) {
            echo "   - Queue: {$job->queue}, Attempts: {$job->attempts}\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ Jobs table error: " . $e->getMessage() . "\n";
    echo "   Run: php artisan queue:table && php artisan migrate\n";
}
echo "\n";

// Test 4: Check GD Extension
echo "4. Checking GD Extension...\n";
if (extension_loaded('gd')) {
    echo "   ✅ GD extension loaded\n";
    $gdInfo = gd_info();
    echo "   GD Version: " . $gdInfo['GD Version'] . "\n";
    echo "   JPEG Support: " . ($gdInfo['JPEG Support'] ? 'YES' : 'NO') . "\n";
    echo "   PNG Support: " . ($gdInfo['PNG Support'] ? 'YES' : 'NO') . "\n";
} else {
    echo "   ❌ GD extension not loaded\n";
}
echo "\n";

// Test 5: Check Compression Settings
echo "5. Checking Compression Settings...\n";
echo "   Threshold: " . (config('media-library.compression_threshold') / 1024 / 1024) . " MB\n";
echo "   Quality: " . config('media-library.compression_quality') . "%\n";
echo "   Max Width: " . config('media-library.max_image_width') . "px\n";
echo "   Max Height: " . config('media-library.max_image_height') . "px\n";
echo "\n";

// Test 6: Check Temp Directory
echo "6. Checking Temp Directory...\n";
$tempDir = sys_get_temp_dir();
echo "   Temp dir: $tempDir\n";
echo "   Writable: " . (is_writable($tempDir) ? 'YES' : 'NO') . "\n";
echo "\n";

// Test 7: Memory and Time Limits
echo "7. Checking PHP Limits...\n";
echo "   Memory Limit: " . ini_get('memory_limit') . "\n";
echo "   Max Execution Time: " . ini_get('max_execution_time') . "s\n";
echo "   Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   Post Max Size: " . ini_get('post_max_size') . "\n";
echo "\n";

// Test 8: Test Image Compression Speed
echo "8. Testing Image Compression Speed...\n";
echo "   Creating test image...\n";

try {
    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
    
    // Create a test image
    $testImage = imagecreatetruecolor(3000, 3000);
    $tempFile = sys_get_temp_dir() . '/test_image_' . time() . '.jpg';
    imagejpeg($testImage, $tempFile, 100);
    imagedestroy($testImage);
    
    $fileSize = filesize($tempFile);
    echo "   Test image size: " . round($fileSize / 1024 / 1024, 2) . " MB\n";
    
    // Test compression
    $startTime = microtime(true);
    $image = $manager->read($tempFile);
    $image->scale(width: 1600);
    $encoded = $image->toJpeg(quality: 80);
    $endTime = microtime(true);
    
    $compressionTime = round($endTime - $startTime, 2);
    echo "   ✅ Compression time: {$compressionTime}s\n";
    
    if ($compressionTime > 5) {
        echo "   ⚠️  WARNING: Compression is slow! Consider:\n";
        echo "      - Reducing max_image_width/height\n";
        echo "      - Lowering compression_quality\n";
        echo "      - Increasing PHP memory_limit\n";
    }
    
    // Cleanup
    @unlink($tempFile);
    
} catch (\Exception $e) {
    echo "   ❌ Compression test failed: " . $e->getMessage() . "\n";
}
echo "\n";

echo "========================================\n";
echo "Diagnostic Complete!\n";
echo "========================================\n";
