<?php

namespace App\Domains\Admin\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadTourVideoAction
{
    public function execute(UploadedFile $file, ?string $oldPath = null): string
    {
        $disk = config('filesystems.default');

        if ($oldPath) {
            Storage::disk($disk)->delete($oldPath);
        }

        $ext = strtolower($file->getClientOriginalExtension()) ?: 'mp4';
        $filename = 'tour_video_'.Str::random(12).'.'.$ext;

        return $file->storeAs('tour-videos', $filename, $disk);
    }
}
