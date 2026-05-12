<?php

namespace App\Models;

use App\Services\EventLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        // ⚡ Bolt Performance Optimization:
        // Cache settings forever to eliminate redundant database queries during a single request.
        // Cache is invalidated on model `saved` and `deleted` events.
        return Cache::rememberForever(
            "settings.{$key}",
            fn () => static::query()->where('key', $key)->value('value')
        ) ?? $default;
    }

    protected static function booted(): void
    {
        // Invalidate the cache when a setting is created or updated
        static::saved(function (Setting $setting): void {
            Cache::forget("settings.{$setting->key}");
        });

        // Invalidate the cache when a setting is deleted
        static::deleted(function (Setting $setting): void {
            Cache::forget("settings.{$setting->key}");
        });

        static::created(function (Setting $setting): void {
            app(EventLogger::class)->record(
                type: 'setting.created',
                summary: "Setting {$setting->key} created",
                subject: $setting,
                userId: auth()->id(),
                metadata: ['key' => $setting->key],
            );
        });

        static::updated(function (Setting $setting): void {
            app(EventLogger::class)->record(
                type: 'setting.updated',
                summary: "Setting {$setting->key} updated",
                subject: $setting,
                userId: auth()->id(),
                metadata: [
                    'key' => $setting->key,
                    'changed' => array_keys($setting->getChanges()),
                ],
            );
        });
    }
}
