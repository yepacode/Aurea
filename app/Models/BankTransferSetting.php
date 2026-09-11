<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransferSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'account_type',
        'document_type',
        'document_number',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }
}
