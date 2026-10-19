<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Descarga el catálogo de Miracle siguiendo la paginación del endpoint.
 *
 * El endpoint de producción pagina (`?page=N`) y el de desarrollo puede
 * devolver todo de una. Para no quedarnos con la primera página creyendo
 * que es el catálogo completo, reconocemos los nombres de paginación más
 * comunes (`meta.total_paginas`, `meta.last_page`, `last_page`,
 * `next_page_url`) y, si no hay ninguno, cortamos cuando una página
 * vuelve vacía o deja de traer referencias nuevas.
 */
class MiracleCatalog
{
    /** Tope de seguridad para no quedarnos en un bucle si el endpoint repite páginas. */
    private const MAX_PAGINAS = 200;

    /**
     * Devuelve los productos indexados por referencia en minúsculas.
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws RuntimeException si el endpoint no responde o devuelve algo inesperado.
     */
    public function porReferencia(?callable $onPage = null, array $filtros = []): array
    {
        $byRef = [];

        foreach ($this->paginas($onPage, $filtros) as $fila) {
            $ref = mb_strtolower(trim((string) ($fila['referencia'] ?? '')));
            if ($ref !== '') {
                $byRef[$ref] = $fila;
            }
        }

        if ($byRef === []) {
            throw new RuntimeException('El catálogo de Miracle llegó vacío.');
        }

        return $byRef;
    }

    /**
     * Recorre todas las páginas y devuelve las filas planas.
     *
     * @param  callable|null  $onPage  fn(int $pagina, int $filasEnPagina, int $acumulado)
     * @param  array<string, scalar>  $filtros  parámetros extra de la query (ej. ['referencia' => '1001'])
     * @return array<int, array<string, mixed>>
     */
    public function paginas(?callable $onPage = null, array $filtros = []): array
    {
        $url     = (string) config('miracle.api_url');
        $token   = (string) config('miracle.api_token');
        $timeout = (int) config('miracle.timeout', 120);

        if ($url === '' || $token === '') {
            throw new RuntimeException('Falta MIRACLE_API_URL o MIRACLE_API_TOKEN en el .env.');
        }

        $filas    = [];
        $vistas   = [];
        $pagina   = 1;
        $ultima   = null;

        do {
            $params = ['token' => $token] + array_filter(
                $filtros,
                static fn ($v) => $v !== null && $v !== ''
            );
            if ($pagina > 1) {
                $params['page'] = $pagina;
            }

            try {
                $resp = Http::timeout($timeout)->acceptJson()->get($url, $params);
            } catch (\Throwable $e) {
                throw new RuntimeException('No se pudo conectar con Miracle: '.$e->getMessage(), 0, $e);
            }

            if ($resp->status() === 401) {
                throw new RuntimeException(
                    'Miracle rechazó el token (401). Revisa MIRACLE_API_TOKEN: el token de '
                    .'desarrollo no sirve contra producción.'
                );
            }

            if (! $resp->successful()) {
                throw new RuntimeException(
                    'Miracle respondió HTTP '.$resp->status().': '.Str::limit((string) $resp->body(), 200)
                );
            }

            $json = $resp->json();
            if (! is_array($json)) {
                throw new RuntimeException('Miracle devolvió una respuesta que no es JSON.');
            }

            // `success:false` explícito es un error; su ausencia no lo es.
            if (array_key_exists('success', $json) && ! $json['success']) {
                throw new RuntimeException(
                    'Miracle respondió success=false: '.(string) ($json['message'] ?? 'sin detalle')
                );
            }

            $data = $json['data'] ?? null;
            if (! is_array($data)) {
                throw new RuntimeException('La respuesta de Miracle no trae un arreglo `data`.');
            }

            // Si una página repite exactamente lo que ya vimos, el endpoint ignora
            // `page` y seguir pidiendo sería un bucle infinito.
            $nuevas = 0;
            foreach ($data as $fila) {
                $ref = mb_strtolower(trim((string) ($fila['referencia'] ?? '')));
                if ($ref !== '' && isset($vistas[$ref])) {
                    continue;
                }
                if ($ref !== '') {
                    $vistas[$ref] = true;
                }
                $filas[] = $fila;
                $nuevas++;
            }

            if ($onPage) {
                $onPage($pagina, count($data), count($filas));
            }

            $ultima ??= $this->ultimaPagina($json);

            $hayMas = $ultima !== null
                ? $pagina < $ultima
                : ($nuevas > 0 && count($data) > 0 && $this->tieneSiguiente($json));

            $pagina++;
        } while ($hayMas && $pagina <= self::MAX_PAGINAS);

        return $filas;
    }

    /** Número de la última página, si el endpoint lo declara con algún nombre conocido. */
    private function ultimaPagina(array $json): ?int
    {
        foreach ([
            $json['meta']['total_paginas'] ?? null,
            $json['meta']['last_page'] ?? null,
            $json['total_paginas'] ?? null,
            $json['last_page'] ?? null,
        ] as $valor) {
            if (is_numeric($valor) && (int) $valor > 0) {
                return (int) $valor;
            }
        }

        return null;
    }

    /** Señal de "hay página siguiente" cuando no se declara el total. */
    private function tieneSiguiente(array $json): bool
    {
        foreach ([
            $json['links']['next'] ?? null,
            $json['next_page_url'] ?? null,
            $json['meta']['next_page_url'] ?? null,
        ] as $valor) {
            if (! empty($valor)) {
                return true;
            }
        }

        return false;
    }
}
