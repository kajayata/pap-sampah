<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ImageStorageService
{
    public const MAX_SIDE_PX = 1280;
    public const JPEG_QUALITY = 70;
    public const MAX_FILE_SIZE_BYTES = 10 * 1024 * 1024; // 10MB

    /**
     * Process uploaded image: validate, auto-orient EXIF, resize if > 1280px, compress JPEG 70%, and store.
     *
     * @param UploadedFile $file
     * @param string $folder Directory prefix (e.g. 'reports' or 'cleanup')
     * @return array Metadata of the processed image
     * @throws InvalidArgumentException
     */
    public function processAndStore(UploadedFile $file, string $folder = 'reports'): array
    {
        if ($file->getSize() > self::MAX_FILE_SIZE_BYTES) {
            throw new InvalidArgumentException('Ukuran file foto melebihi batas maksimum 10MB.');
        }

        $realPath = $file->getRealPath();
        $rawContent = file_get_contents($realPath);
        if ($rawContent === false) {
            throw new InvalidArgumentException('Gagal membaca file gambar.');
        }

        $sourceImage = @imagecreatefromstring($rawContent);
        if (!$sourceImage) {
            throw new InvalidArgumentException('Format gambar tidak valid atau rusak.');
        }

        // Fix EXIF orientation if available (essential for mobile camera uploads)
        $sourceImage = $this->autoOrientExif($sourceImage, $realPath);

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // Calculate resize dimensions keeping aspect ratio
        $targetWidth = $origWidth;
        $targetHeight = $origHeight;
        $maxSide = max($origWidth, $origHeight);

        if ($maxSide > self::MAX_SIDE_PX) {
            $ratio = self::MAX_SIDE_PX / $maxSide;
            $targetWidth = (int) round($origWidth * $ratio);
            $targetHeight = (int) round($origHeight * $ratio);
        }

        // Resample image
        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve colors in truecolor
        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);

        // Fill white background for transparent PNG/WEBP before converting to JPEG
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefilledrectangle($finalImage, 0, 0, $targetWidth, $targetHeight, $white);

        imagecopyresampled(
            $finalImage,
            $sourceImage,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        // Compress to JPEG with quality 70%
        ob_start();
        imagejpeg($finalImage, null, self::JPEG_QUALITY);
        $jpegData = ob_get_clean();

        // Free GD memory
        imagedestroy($sourceImage);
        imagedestroy($finalImage);

        if ($jpegData === false || strlen($jpegData) === 0) {
            throw new InvalidArgumentException('Gagal mengompresi gambar.');
        }

        // Generate unique storage key: folder/YYYY/MM/{uniqid}_{random}.jpg
        $year = date('Y');
        $month = date('m');
        $filename = uniqid('img_', true) . '_' . Str::random(12) . '.jpg';
        $storageKey = "{$folder}/{$year}/{$month}/{$filename}";

        // Store to public disk (or configured cloud storage)
        $disk = config('filesystems.default', 'public');
        Storage::disk($disk)->put($storageKey, $jpegData);

        return [
            'storage_key' => $storageKey,
            'mime_type' => 'image/jpeg',
            'file_size' => strlen($jpegData),
            'width' => $targetWidth,
            'height' => $targetHeight,
            'url' => self::getUrl($storageKey),
        ];
    }

    /**
     * Resolve public URL from storage key.
     */
    public static function getUrl(?string $storageKey): ?string
    {
        if (empty($storageKey)) {
            return null;
        }

        if (str_starts_with($storageKey, 'http://') || str_starts_with($storageKey, 'https://')) {
            return $storageKey;
        }

        $disk = config('filesystems.default', 'public');
        return Storage::disk($disk)->url($storageKey);
    }

    /**
     * Auto rotate GD image based on EXIF Orientation tag from camera.
     */
    protected function autoOrientExif($image, string $filePath)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($filePath);
        if (!$exif || !isset($exif['Orientation'])) {
            return $image;
        }

        switch ($exif['Orientation']) {
            case 3:
                $rotated = imagerotate($image, 180, 0);
                imagedestroy($image);
                return $rotated;
            case 6:
                $rotated = imagerotate($image, -90, 0);
                imagedestroy($image);
                return $rotated;
            case 8:
                $rotated = imagerotate($image, 90, 0);
                imagedestroy($image);
                return $rotated;
            default:
                return $image;
        }
    }
}
