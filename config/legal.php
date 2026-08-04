<?php

/*
|--------------------------------------------------------------------------
| Datos legales de la empresa (Belleza Áurea)
|--------------------------------------------------------------------------
|
| EDITA SOLO ESTE ARCHIVO para completar la información legal. Todos los
| valores entre corchetes [ ] son PLACEHOLDERS: reemplázalos por los datos
| reales de la empresa y valida el contenido con tu abogado antes de
| publicar. Estos datos alimentan automáticamente las páginas:
|   - Términos y condiciones        (/terminos-y-condiciones)
|   - Política de privacidad        (/politica-de-privacidad)
|   - Política de cookies           (/politica-de-cookies)
|
*/

return [

    // Nombre comercial (marca). Se muestra tal cual en las páginas.
    'brand_name'   => 'Belleza Áurea',

    // Razón social (nombre legal registrado en Cámara de Comercio).
    'company_name' => '[RAZÓN SOCIAL S.A.S.]',

    // Identificación tributaria.
    'nit'          => '[NIT 000.000.000-0]',

    // Domicilio / dirección de notificaciones.
    'address'      => '[DIRECCIÓN]',
    'city'         => '[CIUDAD]',
    'country'      => 'Colombia',

    // Datos de contacto para PQR y ejercicio de derechos (Habeas Data).
    'email'        => '[correo@bellezaaurea.com]',
    'phone'        => '[+57 000 000 0000]',
    'whatsapp'     => '[+57 000 000 0000]',

    // Sitio web (por defecto toma la URL de la app).
    'website'      => env('APP_URL', 'https://bellezaaurea.com'),

    // Fecha de última actualización que se muestra en las páginas.
    // Formato libre; ejemplo: '28 de julio de 2026'.
    'updated_at'   => '28 de julio de 2026',

    // Días hábiles para el derecho de retracto (Estatuto del Consumidor).
    'retracto_dias' => 5,
];
