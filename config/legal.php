<?php

/*
|--------------------------------------------------------------------------
| Datos legales de la empresa (Belleza Áurea)
|--------------------------------------------------------------------------
|
| EDITA SOLO ESTE ARCHIVO para completar la información legal. Los valores
| que estén en blanco ('') se ocultarán automáticamente en las vistas.
| Estos datos alimentan automáticamente las páginas:
|   - Términos y condiciones        (/terminos-y-condiciones)
|   - Política de privacidad        (/politica-de-privacidad)
|   - Política de cookies           (/politica-de-cookies)
|
*/

return [

    // Nombre comercial (marca). Se muestra tal cual en las páginas.
    'brand_name'   => 'Belleza Áurea',

    // Razón social (nombre legal registrado en Cámara de Comercio).
    'company_name' => 'Belleza Áurea',

    // Identificación tributaria.
    'nit'          => '',

    // Domicilio / dirección de notificaciones.
    'address'      => 'Bucaramanga, Colombia',
    'city'         => 'Bucaramanga',
    'country'      => 'Colombia',

    // Datos de contacto para PQR y ejercicio de derechos (Habeas Data).
    'email'        => 'contacto@bellezaaurea.com',
    'phone'        => '+57 317 0453950',
    'whatsapp'     => '573170453950',

    // Sitio web (por defecto toma la URL de la app).
    'website'      => env('APP_URL', 'https://bellezaaurea.com'),

    // Fecha de última actualización que se muestra en las páginas.
    // Formato libre; ejemplo: '28 de julio de 2026'.
    'updated_at'   => '28 de julio de 2026',

    // Días hábiles para el derecho de retracto (Estatuto del Consumidor).
    'retracto_dias' => 5,
];
