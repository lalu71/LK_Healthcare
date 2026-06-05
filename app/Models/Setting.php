<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isShutdown(): bool
    {
        return static::get('site_shutdown', '0') === '1';
    }

    public static function setShutdown(bool $on): void
    {
        static::put('site_shutdown', $on ? '1' : '0');
    }
}
