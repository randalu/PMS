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
        // ⚡ Bolt: Cache settings to avoid repeated DB queries for global settings.
        // Fallback is evaluated outside the closure to prevent caching the default value.
        $value = Cache::rememberForever("settings.{$key}", function () use ($key) {
            return static::query()->where('key', $key)->value('value');
        });

        return $value ?? $default;
    }

    protected static function booted(): void
    {
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

        // ⚡ Bolt: Invalidate cache when setting is modified
        static::saved(function (Setting $setting): void {
            Cache::forget("settings.{$setting->key}");

            if ($setting->isDirty('key') && $setting->getOriginal('key')) {
                Cache::forget("settings.{$setting->getOriginal('key')}");
            }
        });

        static::deleted(function (Setting $setting): void {
            Cache::forget("settings.{$setting->key}");
        });
    }
}
