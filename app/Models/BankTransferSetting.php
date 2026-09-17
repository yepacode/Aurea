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
        // Perfil bancario colombiano y medios digitales (Nequi/Daviplata/
        // Bancolombia a la mano). El almacenamiento sigue siendo key/value:
        // cada campo se guarda como una fila en `value` con `key` = nombre.
        'nit',
        'nequi_enabled', 'nequi_number', 'nequi_instructions',
        'daviplata_enabled', 'daviplata_number', 'daviplata_instructions',
        'bancolombia_amano_enabled', 'bancolombia_amano_number',
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
