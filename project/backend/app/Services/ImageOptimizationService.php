<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image; // Intervention Image v3 (Laravel package)

class ImageOptimizationService
{
    /**
     * Generate multiple sizes and WebP/JPG variants.
     * Returns array of public storage paths.
     *
     * @return array<string,string>
     */
    public function optimize(UploadedFile $file): array
    {
        $sizes = [
            'thumbnail' => 300,
            'medium' => 800,
            'large' => 1200,
        ];

        $baseName = pathinfo($file->hashName('articles'), PATHINFO_FILENAME);
        Storage::disk('public')->makeDirectory('articles');

        $results = [];
        foreach ($sizes as $label => $width) {
            // Create JPEG variant
            $jpegPath = "articles/{$baseName}_{$label}.jpg";
            $this->saveResized($file, $width, 80, $jpegPath, 'jpg');
            $results[$label] = $jpegPath;

            // Create WebP variant
            $webpPath = "articles/{$baseName}_{$label}.webp";
            $this->saveResized($file, $width, 80, $webpPath, 'webp');
            $results[$label . '_webp'] = $webpPath;
        }

        // Original fallback copy
        $origExt = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $originalPath = $file->storeAs('articles', $baseName . '_orig.' . $origExt, 'public');
        $results['original'] = $originalPath;

        return $results;
    }

    private function saveResized(UploadedFile $file, int $width, int $quality, string $path, string $format): void
    {
        $img = Image::read($file->getPathname());
        $img = $img->scale(width: $width);
        $encoded = match ($format) {
            'webp' => $img->toWebp($quality),
            'jpg', 'jpeg' => $img->toJpeg($quality),
            'png' => $img->toPng(),
            default => $img->toJpeg($quality),
        };
        Storage::disk('public')->put($path, $encoded);
    }
}
