<?php

namespace App\Support;

/**
 * Sanitizador HTML mínimo — sin dependencias — para contenido editable por
 * admin que se rendee con `{!! ... !!}`. Elimina scripts, iframes, objetos,
 * event handlers on*= y esquemas peligrosos (javascript:, data:) en href/src.
 *
 * NO uses este sanitizador sobre bloques JSON-LD (`<script type="application/ld+json">`);
 * ahí valida al guardar (ver HandlesSeoInput::seoRules() para custom_schema_markup).
 */
class SafeHtml
{
    public static function sanitize(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        // Quitar tags peligrosos con contenido (script, iframe, object, embed, style, link, meta).
        $html = preg_replace(
            '#<\s*(script|iframe|object|embed|style|link|meta)[^>]*>.*?<\s*/\s*\1\s*>#is',
            '',
            $html
        );

        // Quitar los mismos tags si vienen self-closing o sueltos.
        $html = preg_replace(
            '#<\s*(script|iframe|object|embed|style|link|meta)[^>]*/?>#i',
            '',
            $html
        );

        // Quitar event handlers inline (onclick=, onerror=, onmouseover=, ...).
        $html = preg_replace(
            '#\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)#i',
            '',
            $html
        );

        // Neutralizar esquemas javascript: y data: en href/src.
        $html = preg_replace(
            '#(href|src)\s*=\s*"(?:javascript|data):[^"]*"#i',
            '$1="#"',
            $html
        );
        $html = preg_replace(
            "#(href|src)\s*=\s*'(?:javascript|data):[^']*'#i",
            '$1="#"',
            $html
        );

        return $html ?? '';
    }
}
