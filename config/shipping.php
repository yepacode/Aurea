<?php

/**
 * Config del sistema de envíos multi-zona.
 *
 * `fallback_cost` es el costo que se usa si el departamento cotizado no está
 * asignado a ninguna zona activa. Debe existir SIEMPRE para que el checkout
 * no falle cuando el admin todavía no ha configurado las zonas.
 *
 * `carriers` es el catálogo (label ↔ slug) de transportadoras seleccionables
 * en el admin. El slug se guarda en `shipping_zones.carrier`; el label se usa
 * como valor de `orders.shipping_carrier` para que se vea igual en los correos
 * que ya envía la tienda.
 */
return [
    'fallback_cost' => (int) env('SHIPPING_FALLBACK_COST', 20000),

    'default_weight_kg' => 1,

    'carriers' => [
        'servientrega'    => 'Servientrega',
        'interrapidisimo' => 'Interrapidísimo',
        'coordinadora'    => 'Coordinadora',
        'envia'           => 'Envía',
        'tcc'             => 'TCC',
        'deprisa'         => 'Deprisa',
        '4-72'            => '4-72',
        'propio'          => 'Entrega personal',
        'otro'            => 'Otro',
    ],

    /**
     * Los 32 departamentos + Bogotá D.C. que usa el select del checkout.
     * Fuente de verdad para el multi-select del admin.
     */
    'departments' => [
        'Amazonas',
        'Antioquia',
        'Arauca',
        'Atlántico',
        'Bogotá D.C.',
        'Bolívar',
        'Boyacá',
        'Caldas',
        'Caquetá',
        'Casanare',
        'Cauca',
        'Cesar',
        'Chocó',
        'Córdoba',
        'Cundinamarca',
        'Guainía',
        'Guaviare',
        'Huila',
        'La Guajira',
        'Magdalena',
        'Meta',
        'Nariño',
        'Norte de Santander',
        'Putumayo',
        'Quindío',
        'Risaralda',
        'San Andrés y Providencia',
        'Santander',
        'Sucre',
        'Tolima',
        'Valle del Cauca',
        'Vaupés',
        'Vichada',
    ],
];
