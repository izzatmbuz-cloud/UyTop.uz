<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function number(string $key, float $default): float
    {
        return (float) Cache::remember("platform-setting.{$key}", now()->addHour(), fn () => static::where('key', $key)->value('value') ?? $default);
    }

    public static function putNumber(string $key, float $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget("platform-setting.{$key}");
    }
}
