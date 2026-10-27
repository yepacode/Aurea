<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ganadores curados ("Más vendidos")
    |--------------------------------------------------------------------------
    |
    | Códigos de productos (internal_code o miracle_ref) que se usan para
    | RELLENAR la sección "Más vendidos" del home cuando aún no hay suficientes
    | ventas ONLINE pagadas para completar la fila de 8.
    |
    | - Las ventas online reales SIEMPRE van primero y llevan el badge
    |   "#N en ventas". Estos curados van después y NO llevan badge (no se
    |   inventan posiciones de venta).
    | - El orden de esta lista = prioridad de relleno.
    | - Edita libremente: agrega/quita códigos o reordénalos. Tras cambiarla,
    |   corre `php artisan config:clear` (y la caché del home expira sola en 5 min).
    |
    | Fuente inicial: top de ventas históricas/mayoreo de Belleza Áurea.
    */

    'curated' => [
        '1027-A', '1276', '1004-B', '3001', '1056-30ml', '1101', '1308', '1133', '1054', '1068',
        '1284', '1001', '1125', '1110', '1304', '1026', '1004-A', '1004', '1066', '1228',
        '1003', '1270', '1002', '1291', '1067', '1030-A', '1124', '1294', '1056-60ml', '1075',
        '1062', '1030', '1223', '1057', '1208', '1111', '1311', '1264', '1303', '1092',
        '1138', '1064', '1215', '1043', '1260', '1128', '2039', '1216', '1083', '1292',
    ],

];
