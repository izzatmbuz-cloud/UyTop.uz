<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ListingImageService
{
    public function store(UploadedFile $file): array
    {
        $dimensions = getimagesize($file->getRealPath());
        if ($dimensions === false || $dimensions[0] > 8000 || $dimensions[1] > 8000 || ($dimensions[0] * $dimensions[1]) > 40000000) {
            throw new RuntimeException('Rasm o‘lchami juda katta.');
        }

        $source = match ($file->getMimeType()) {
            'image/jpeg' => imagecreatefromjpeg($file->getRealPath()),
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/webp' => imagecreatefromwebp($file->getRealPath()),
            default => false,
        };

        if ($source === false) {
            throw new RuntimeException('Rasmni qayta ishlashning imkoni bo‘lmadi.');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, 1600 / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        $background = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $background);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagewebp($target, null, 84);
        $contents = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);

        if (! is_string($contents)) {
            throw new RuntimeException('Rasmni saqlashning imkoni bo‘lmadi.');
        }

        $path = 'listings/'.now()->format('Y/m').'/'.Str::uuid().'.webp';
        Storage::disk('public')->put($path, $contents);

        return ['storage_path' => $path, 'mime' => 'image/webp'];
    }
}
