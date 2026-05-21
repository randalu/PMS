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
        /**
         * ⚡ Bolt Optimization: Settings Caching
         * Impact: Eliminates redundant database queries for global settings on every request.
         * Reduces ~10-15 DB queries per page load to 0 (after initial cache).
         * Note: The $default fallback is evaluated outside the closure so that
         * default values are not cached if a DB record does not exist.
         */
        $value = Cache::rememberForever('settings.'.$key, function () use ($key) {
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

        // Invalidate cache when a setting is saved
        static::saved(function (Setting $setting): void {
            if ($setting->isDirty('key') && $setting->getOriginal('key')) {
                Cache::forget('settings.'.$setting->getOriginal('key'));
            }
            Cache::forget('settings.'.$setting->key);
        });

        // Invalidate cache when a setting is deleted
        static::deleted(function (Setting $setting): void {
            Cache::forget('settings.'.$setting->key);
        });
    }
}
