<?php

namespace App\Services;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final class LegacyMediaImporter
{
    private const FOLDERS = [
        'اراء العملاء', 'المشاريع', 'انشاء متاجر الكترونية', 'سوشال ميديا',
        'صوتيات', 'لوقو العملاء', 'نتائج الحملات الاعلانية',
    ];

    public function import(): array
    {
        $legacyRoot = dirname(base_path());
        $imported = 0;
        $verified = 0;
        $variants = 0;

        if (File::exists($legacyRoot.'/logo.webp')) {
            [$wasImported, $variant] = $this->copyPublic($legacyRoot.'/logo.webp', 'logo.webp');
            $imported += $wasImported; $verified++; $variants += $variant;
        }

        foreach (self::FOLDERS as $folder) {
            $source = $legacyRoot.DIRECTORY_SEPARATOR.$folder;
            if (! File::isDirectory($source)) continue;

            foreach (File::allFiles($source) as $file) {
                $relative = $folder.'/'.str_replace('\\', '/', $file->getRelativePathname());
                [$wasImported, $variant] = $this->copyPublic($file->getPathname(), $relative);
                $imported += $wasImported; $verified++; $variants += $variant;
            }
        }

        $freebies = $legacyRoot.DIRECTORY_SEPARATOR.'ملفات مجانية';
        if (File::isDirectory($freebies)) {
            foreach (File::allFiles($freebies) as $file) {
                $target = 'freebies/'.str_replace('\\', '/', $file->getRelativePathname());
                $contents = File::get($file->getPathname());
                if (! Storage::disk('local')->exists($target) || hash('sha256', Storage::disk('local')->get($target)) !== hash('sha256', $contents)) {
                    Storage::disk('local')->put($target, $contents);
                    $imported++;
                }
                $verified++;
            }
        }

        return compact('imported', 'verified', 'variants');
    }

    public function syncPublicStorage(): array
    {
        $synced = 0;

        foreach (Storage::disk('public')->allFiles('media/source') as $path) {
            if (MediaAsset::where('path', $path)->exists()) {
                continue;
            }

            $contents = Storage::disk('public')->get($path);
            $originalName = basename(str_replace('\\', '/', $path));

            MediaAsset::create([
                'disk' => 'public',
                'path' => $path,
                'original_name' => $originalName,
                'mime_type' => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
                'size' => Storage::disk('public')->size($path),
                'checksum' => hash('sha256', $contents),
                'alt' => [
                    'ar' => pathinfo($originalName, PATHINFO_FILENAME),
                    'en' => pathinfo($originalName, PATHINFO_FILENAME),
                ],
                'metadata' => ['source' => str_replace('media/source/', '', $path)],
            ]);

            $synced++;
        }

        return compact('synced');
    }

    private function copyPublic(string $source, string $relative): array
    {
        $path = 'media/source/'.$relative;
        $contents = File::get($source);
        $checksum = hash('sha256', $contents);
        $wasImported = 0;

        if (! Storage::disk('public')->exists($path) || hash('sha256', Storage::disk('public')->get($path)) !== $checksum) {
            Storage::disk('public')->put($path, $contents);
            $wasImported = 1;
        }

        $mime = File::mimeType($source) ?: 'application/octet-stream';
        $metadata = ['source' => $relative];
        $variantPath = $this->createWebpVariant($source, $checksum, $mime);
        if ($variantPath) $metadata['webp'] = $variantPath;

        MediaAsset::updateOrCreate(['path' => $path], [
            'disk' => 'public',
            'original_name' => basename($source),
            'mime_type' => $mime,
            'size' => File::size($source),
            'checksum' => $checksum,
            'alt' => ['ar' => pathinfo(basename($source), PATHINFO_FILENAME), 'en' => pathinfo(basename($source), PATHINFO_FILENAME)],
            'metadata' => $metadata,
        ]);

        return [$wasImported, $variantPath ? 1 : 0];
    }

    private function createWebpVariant(string $source, string $checksum, string $mime): ?string
    {
        if (! function_exists('imagewebp') || ! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) return null;
        $path = 'media/variants/'.$checksum.'.webp';
        if (Storage::disk('public')->exists($path)) return $path;

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source),
        };
        if (! $image) return null;

        $width = imagesx($image); $height = imagesy($image); $max = 1600;
        $ratio = min(1, $max / max($width, 1));
        $target = imagecreatetruecolor((int) round($width * $ratio), (int) round($height * $ratio));
        imagealphablending($target, false); imagesavealpha($target, true);
        imagecopyresampled($target, $image, 0, 0, 0, 0, imagesx($target), imagesy($target), $width, $height);
        ob_start(); imagewebp($target, null, 82); $data = ob_get_clean();
        imagedestroy($image); imagedestroy($target);
        if ($data === false) return null;
        Storage::disk('public')->put($path, $data);
        return $path;
    }
}
