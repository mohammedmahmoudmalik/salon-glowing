<?php

namespace App\Domains\Service\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceCategory extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'slug', 'image', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ServiceCategory $model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->name_en);
            }
        });

        static::deleting(function (ServiceCategory $model) {
            if ($model->image && ! str_starts_with($model->image, 'http')) {
                Storage::disk(config('filesystems.default'))->delete($model->image);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$count++;
        }

        return $slug;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk(config('filesystems.default'))->url($this->image);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
