<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Descuento por defecto para mayoristas
    |--------------------------------------------------------------------------
    |
    | Multiplicador aplicado al `price` de un producto cuando la columna
    | `wholesale_price` es NULL y el cliente autenticado es mayorista aprobado.
    |
    | 0.80 = 20 % de descuento sobre el precio de venta público.
    |
    */

    'default_discount' => 0.80,

    /*
    |--------------------------------------------------------------------------
    | Rangos de volumen mensual estimado
    |--------------------------------------------------------------------------
    |
    | Opciones que verá la distribuidora al llenar la solicitud.
    |
    */

    'monthly_volume_options' => [
        'menos_500k' => 'Menos de $500 000',
        '500k_1m'    => '$500 000 – $1 000 000',
        '1m_3m'      => '$1 000 000 – $3 000 000',
        '3m_5m'      => '$3 000 000 – $5 000 000',
        'mas_5m'     => 'Más de $5 000 000',
    ],

];
