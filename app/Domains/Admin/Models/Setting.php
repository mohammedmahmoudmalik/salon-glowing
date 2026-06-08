<?php

namespace App\Domains\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::remember("setting.{$key}", 3600, fn () => static::where('key', $key)->first()?->value
        );

        return $value ?? $default;
    }

    public static function set(string $key, mixed $value): static
    {
        Cache::forget("setting.{$key}");

        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
