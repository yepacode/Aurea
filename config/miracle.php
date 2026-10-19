<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Conexión con el catálogo de Miracle (solo lectura por URL)
    |--------------------------------------------------------------------------
    |
    | Áurea se sincroniza jalando el JSON del endpoint de Miracle. Aquí solo
    | viven la URL y el token; toda la lógica está en el comando
    | `aurea:sync-miracle`. No hay pantallas ni front: es 100% backend.
    |
    */

    'api_url'   => env('MIRACLE_API_URL', 'http://127.0.0.1:8030/api/v1/aurea/productos'),
    'api_token' => env('MIRACLE_API_TOKEN', ''),
    'timeout'   => (int) env('MIRACLE_API_TIMEOUT', 120),
];
