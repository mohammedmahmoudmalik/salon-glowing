<?php

namespace App\Domains\Admin\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadOwnerVideoAction
{
    public function execute(UploadedFile $file, ?string $oldPath = null): string
    {
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $ext = strtolower($file->getClientOriginalExtension()) ?: 'mp4';
        $filename = 'owner_video_'.Str::random(12).'.'.$ext;

        return $file->storeAs('owner-videos', $filename, 'public');
    }
}
