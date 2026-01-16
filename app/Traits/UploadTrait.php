<?php

namespace App\Traits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait UploadTrait {

  public function uploadAllTyps($file, $directory, $width = null, $height = null)
  {
      try {
          // Log detailed information about the file
          Log::info('uploadAllTyps called', [
              'file_type' => gettype($file),
              'is_object' => is_object($file),
              'class' => is_object($file) ? get_class($file) : 'not an object',
              'directory' => $directory
          ]);

          // Ensure the file is valid
          if (!$file) {
              Log::warning('Null file provided to uploadAllTyps');
              return 'default.png';
          }

          // Handle UploadedFile objects
          if (is_object($file) && method_exists($file, 'getClientMimeType')) {
              // Create directory if it doesn't exist
              $thumbsPath = "storage/images/{$directory}";
              if (!File::isDirectory($thumbsPath)) {
                  File::makeDirectory($thumbsPath, 0777, true, true);
              }

              // Get file mime type
              $fileMimeType = $file->getClientMimeType();
              $imageCheck = explode('/', $fileMimeType);

              // Handle image files
              if ($imageCheck[0] == 'image') {
                  $allowedImagesMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml'];
                  if (!in_array($fileMimeType, $allowedImagesMimeTypes)) {
                      Log::warning("Unsupported image type: {$fileMimeType}");
                      return 'default.png';
                  }

                  return $this->uploadeImage($file, $directory, $width, $height);
              }

              // Handle other file types
              $allowedMimeTypes = [
                  'application/pdf',
                  'application/msword',
                  'application/excel',
                  'application/vnd.ms-excel',
                  'application/vnd.msexcel',
                  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                  'application/octet-stream'
              ];

              if (!in_array($fileMimeType, $allowedMimeTypes)) {
                  Log::warning("Unsupported file type: {$fileMimeType}");
                  return 'default.png';
              }

              return $this->uploadFile($file, $directory);
          }
          // Handle string inputs (file paths or base64)
          elseif (is_string($file)) {
              if (file_exists($file)) {
                  // It's a file path, try to determine if it's an image
                  $mimeType = mime_content_type($file);
                  $imageCheck = explode('/', $mimeType);

                  if ($imageCheck[0] == 'image') {
                      return $this->uploadeImage($file, $directory, $width, $height);
                  } else {
                      // It's another type of file
                      return $this->uploadFile($file, $directory);
                  }
              } else {
                  // It might be base64 data, try to upload as image
                  return $this->uploadeImage($file, $directory, $width, $height);
              }
          }
          // Unsupported input type
          else {
              Log::warning('Unsupported input type for uploadAllTyps', ['type' => gettype($file)]);
              return 'default.png';
          }
      } catch (\Exception $exception) {
          Log::error('File upload error in uploadAllTyps: ' . $exception->getMessage(), [
              'file_type' => gettype($file),
              'trace' => $exception->getTraceAsString()
          ]);
          return 'default.png';
      }
  }

  public function uploadFile($file, $directory)
  {
      try {
          // Log detailed information about the file
          Log::info('uploadFile called', [
              'file_type' => gettype($file),
              'is_object' => is_object($file),
              'class' => is_object($file) ? get_class($file) : 'not an object',
              'directory' => $directory
          ]);

          // Ensure directory exists
          $thumbsPath = "storage/images/{$directory}";
          if (!File::isDirectory($thumbsPath)) {
              File::makeDirectory($thumbsPath, 0777, true, true);
          }

          // Handle UploadedFile objects
          if (is_object($file) && method_exists($file, 'getClientOriginalExtension')) {
              $filename = time() . rand(1000000, 9999999) . '.' . $file->getClientOriginalExtension();

              // Use move method directly instead of storeAs
              $file->move($thumbsPath, $filename);

              return $filename;
          }
          // Handle string inputs (file paths)
          elseif (is_string($file) && file_exists($file)) {
              $pathInfo = pathinfo($file);
              $extension = $pathInfo['extension'] ?? '';
              $filename = time() . rand(1000000, 9999999) . ($extension ? '.' . $extension : '');

              // Copy the file
              copy($file, "{$thumbsPath}/{$filename}");

              return $filename;
          }
          else {
              Log::warning('Unsupported input type for uploadFile', ['type' => gettype($file)]);
              return 'default.png';
          }
      } catch (\Exception $exception) {
          Log::error('File upload error in uploadFile: ' . $exception->getMessage(), [
              'file_type' => gettype($file),
              'trace' => $exception->getTraceAsString()
          ]);
          return 'default.png';
      }
  }

  public function uploadeImage($file, $directory, $width = null, $height = null)
  {
      try {
          // Log detailed information about the file
          Log::info('Uploading image', [
              'file_type' => gettype($file),
              'is_object' => is_object($file),
              'class' => is_object($file) ? get_class($file) : 'not an object',
              'directory' => $directory
          ]);

          // Ensure directory exists
          $thumbsPath = "storage/images/{$directory}";
          if (!File::isDirectory($thumbsPath)) {
              File::makeDirectory($thumbsPath, 0777, true, true);
          }

          // Handle different types of file inputs
          if (is_object($file) && method_exists($file, 'getClientOriginalExtension')) {
              // It's an UploadedFile object
              $extension = $file->getClientOriginalExtension();
              $name = time() . '_' . rand(1111, 9999) . '.' . $extension;

              // Use the move method directly for UploadedFile objects
              $file->move($thumbsPath, $name);

              // If resizing is needed, open the saved file and resize it
              if (null != $width && null != $height) {
                  $manager = new ImageManager(driver: new Driver());
                  $img = $manager->read("{$thumbsPath}/{$name}");
                  $img = $img->resize(width: $width, height: $height);
                  $img->save("{$thumbsPath}/{$name}");
              }

              return (string) $name;
          }
          elseif (is_string($file)) {
              // Create image manager
              $manager = new ImageManager(driver: new Driver());

              // For string inputs (file paths or base64)
              if (file_exists($file)) {
                  // It's a file path
                  $pathInfo = pathinfo($file);
                  $extension = $pathInfo['extension'] ?? 'jpg';
                  $name = time() . '_' . rand(1111, 9999) . '.' . $extension;

                  // Read and process the image
                  $img = $manager->read($file);

                  // Resize if needed
                  if (null != $width && null != $height) {
                      $img = $img->resize(width: $width, height: $height);
                  }

                  // Save the image
                  $img->save("{$thumbsPath}/{$name}");

                  return (string) $name;
              }
              else {
                  // Try to decode as base64
                  try {
                      $name = time() . '_' . rand(1111, 9999) . '.png';
                      $img = $manager->read(base64_decode($file));

                      // Resize if needed
                      if (null != $width && null != $height) {
                          $img = $img->resize(width: $width, height: $height);
                      }

                      // Save the image
                      $img->save("{$thumbsPath}/{$name}");

                      return (string) $name;
                  }
                  catch (\Exception $e) {
                      Log::error('Failed to decode base64 image: ' . $e->getMessage());
                      return 'default.png';
                  }
              }
          }
          else {
              // Unsupported file type
              Log::warning('Unsupported file type for image upload', ['type' => gettype($file)]);
              return 'default.png';
          }
      }
      catch (\Exception $exception) {
          // Log the error for debugging
          Log::error('Image upload error: ' . $exception->getMessage(), [
              'file_type' => gettype($file),
              'trace' => $exception->getTraceAsString()
          ]);

          // Return default image if there's an error
          return 'default.png';
      }
  }

  public function uploadAllTypsOld($file, $directory = 'unknown', $resize1 = null, $resize2 = null) {
    // Create directory if it doesn't exist
    $thumbsPath = "storage/images/{$directory}";
    if (!File::isDirectory($thumbsPath)) {
      File::makeDirectory($thumbsPath, 0777, true, true);
    }

    // Create image manager with GD driver
    $manager = new ImageManager(
        driver: new Driver()
    );

    if (is_file($file)) {
      // Read image from file
      $img = $manager->read($file);
      $name = time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();

      // Resize if dimensions are provided
      if (null != $resize1) {
        $img = $img->resize(width: $resize1, height: $resize2);
      }

      // Save the image
      $img->save("{$thumbsPath}/{$name}");
      return (string) $name;
    } else {
      // For base64 encoded images
      $name = time() . rand(1000000, 9999999) . '.png';

      try {
        // Read image from base64 data
        $img = $manager->read(base64_decode($file));

        // Resize if dimensions are provided
        if (null != $resize1) {
          $img = $img->resize(width: $resize1, height: $resize2);
        }

        // Save the image
        $img->save("{$thumbsPath}/{$name}");
        return (string) $name;
      } catch (\Exception $exception) {
        // Log the error for debugging
        Log::error('Image processing error: ' . $exception->getMessage());
        // Return default image if there's an error
        return 'default.png';
      }
    }
  }

  public function deleteFile($file_name, $directory = 'unknown'): void {
    $filePath = "storage/images/{$directory}/{$file_name}";
    if ($file_name && $file_name != 'default.png' && file_exists($filePath)) {
        try {
            unlink($filePath);
        } catch (\Exception $exception) {
            Log::error("Failed to delete file {$filePath}: " . $exception->getMessage());
        }
    }
  }

  public function defaultImage($directory)
  {
    // Check if the directory-specific default image exists
    $directoryPath = public_path("storage/images/{$directory}");
    $directoryDefaultImage = "{$directoryPath}/default.png";

    if (file_exists($directoryDefaultImage)) {
      return asset("/storage/images/{$directory}/default.png");
    }

    // Fallback to the main default image
    return asset("/storage/images/default.png");
  }

  public static function getImage($name, $directory)
  {
    // Check if the image exists
    $imagePath = public_path("storage/images/{$directory}/{$name}");

    if (file_exists($imagePath)) {
      return asset("storage/images/{$directory}/{$name}");
    }

    // If the image doesn't exist, return the default image
    return (new static)->defaultImage($directory);
  }

}
