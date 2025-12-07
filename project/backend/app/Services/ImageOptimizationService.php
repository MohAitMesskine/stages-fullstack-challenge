<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image; // Intervention Image v2 facade

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
            // JPEG variant
            $jpegPath = "articles/{$baseName}_{$label}.jpg";
            $this->saveResizedV2($file, $width, 80, $jpegPath, 'jpg');
            $results[$label] = $jpegPath;

            // WebP variant (if supported)
            $webpPath = "articles/{$baseName}_{$label}.webp";
            $this->saveResizedV2($file, $width, 80, $webpPath, 'webp');
            $results[$label . '_webp'] = $webpPath;
        }

        // Original fallback copy
        $origExt = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $originalPath = $file->storeAs('articles', $baseName . '_orig.' . $origExt, 'public');
        $results['original'] = $originalPath;

        return $results;
    }

    private function saveResizedV2(UploadedFile $file, int $width, int $quality, string $path, string $format): void
    {
        $img = Image::make($file->getPathname())
            ->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        $encoded = $format === 'webp'
            ? $img->encode('webp', $quality)
            : $img->encode('jpg', $quality);
        Storage::disk('public')->put($path, (string) $encoded);
    }
}
