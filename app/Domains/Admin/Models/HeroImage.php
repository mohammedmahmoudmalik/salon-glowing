<?php

namespace App\Domains\Admin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroImage extends Model
{
    protected $fillable = ['image_path', 'order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getUrlAttribute(): string
    {
        return $this->image_path
            ? Storage::disk(config('filesystems.default'))->url($this->image_path)
            : '';
    }
}
