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
        // ⚡ Bolt: Cache setting queries to prevent redundant DB calls
        // Evaluating $default outside closure ensures fallback isn't cached if setting is missing
        $value = Cache::rememberForever(
            "settings.{$key}",
            fn () => static::query()->where('key', $key)->value('value')
        );

        return $value ?? $default;
    }

    protected static function booted(): void
    {
        static::saved(function (Setting $setting): void {
            if ($setting->isDirty('key') && $setting->getOriginal('key')) {
                Cache::forget("settings.{$setting->getOriginal('key')}");
            }
            Cache::forget("settings.{$setting->key}");
        });

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
