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
        // ⚡ Bolt Optimization: Cache global settings indefinitely.
        // The StorefrontController calls this 6 times per page load.
        // This reduces those 6 database queries to 0 using Cache.
        $value = Cache::rememberForever("settings.{$key}", function () use ($key) {
            return static::query()->where('key', $key)->value('value');
        });

        return $value ?? $default;
    }

    protected static function booted(): void
    {
        // ⚡ Bolt Optimization: Invalidate cache when settings change
        static::saved(fn (Setting $setting) => Cache::forget("settings.{$setting->key}"));
        static::deleted(fn (Setting $setting) => Cache::forget("settings.{$setting->key}"));

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
