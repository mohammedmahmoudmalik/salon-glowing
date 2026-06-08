<?php

namespace App\Domains\Admin\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadOwnerVideoAction
{
    public function execute(UploadedFile $file, ?string $oldPath = null): string
    {
        $disk = config('filesystems.default');

        if ($oldPath) {
            Storage::disk($disk)->delete($oldPath);
        }

        $ext = strtolower($file->getClientOriginalExtension()) ?: 'mp4';
        $filename = 'owner_video_'.Str::random(12).'.'.$ext;

        return $file->storeAs('owner-videos', $filename, $disk);
    }
}
