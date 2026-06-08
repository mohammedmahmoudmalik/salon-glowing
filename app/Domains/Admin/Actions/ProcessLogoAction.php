<?php

namespace App\Domains\Admin\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProcessLogoAction
{
    // Maximum display dimensions for the logo area (navbar: 160×50, footer: 150×48)
    private const MAX_WIDTH = 400;

    private const MAX_HEIGHT = 200;

    public function execute(UploadedFile $file, ?string $oldPath = null): string
    {
        if ($oldPath) {
            Storage::disk(config('filesystems.default'))->delete($oldPath);
        }

        $mime = $file->getMimeType();

        // SVG is vector — store as-is, no rasterisation needed
        if ($mime === 'image/svg+xml') {
            return $this->storeDirect($file, 'svg');
        }

        // GD not loaded yet (e.g. server started before extension was enabled)
        // — fall back to direct storage; CSS object-contain handles display
        if (! extension_loaded('gd')) {
            $ext = match ($mime) {
                'image/png' => 'png',
                'image/webp' => 'webp',
                default => 'jpg',
            };

            return $this->storeDirect($file, $ext);
        }

        // GD available — resize to logo box and encode as PNG
        $manager = new ImageManager(new Driver);
        $image = $manager->read($file->getPathname());

        $image->scaleDown(self::MAX_WIDTH, self::MAX_HEIGHT);

        $encoded = $image->toPng();
        $filename = 'logo_'.Str::random(12).'.png';
        $path = 'logos/'.$filename;

        Storage::disk(config('filesystems.default'))->put($path, (string) $encoded);

        return $path;
    }

    private function storeDirect(UploadedFile $file, string $ext): string
    {
        $filename = 'logo_'.Str::random(12).'.'.$ext;
        $path = 'logos/'.$filename;
        Storage::disk(config('filesystems.default'))->put($path, file_get_contents($file->getPathname()));

        return $path;
    }
}
