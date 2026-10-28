<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Sirve cualquier imagen de producto como JPG.
 *
 * Mercado Libre y Meta (Instagram/Facebook Shops) NO aceptan WebP; casi todas
 * las imágenes del sitio son .webp. Este endpoint convierte al vuelo a JPG
 * (con caché en disco) para que los feeds/planillas usen URLs .jpg válidas.
 *
 * URL pública: /img/jpg/<ruta>.jpg   (p. ej. /img/jpg/products/slug/1-xxx.jpg)
 * El SOURCE real puede ser .webp/.png/.jpg/.jpeg/.gif — se busca por base.
 *
 * Seguridad: misma defensa en profundidad que la ruta /storage/{path}.
 */
class ImageJpegController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        // 1) Validar la ruta (bloquea traversal, backslashes, drive letters).
        if (preg_match('/[\\\\:]/', $path) || str_contains($path, '..')) {
            abort(403);
        }

        $baseDir = realpath(storage_path('app/public'));
        if ($baseDir === false) {
            abort(404);
        }

        // 2) Resolver el archivo fuente. La URL termina en .jpg, pero el archivo
        //    real puede tener otra extensión: probamos base + extensiones.
        $base = preg_replace('/\.(jpe?g|webp|png|gif)$/i', '', $path);
        $source = null;
        $sourceExt = null;
        foreach (['jpg', 'jpeg', 'webp', 'png', 'gif'] as $ext) {
            $candidate = realpath($baseDir . DIRECTORY_SEPARATOR . $base . '.' . $ext);
            if ($candidate !== false
                && str_starts_with($candidate, $baseDir . DIRECTORY_SEPARATOR)
                && is_file($candidate)) {
                $source = $candidate;
                $sourceExt = $ext;
                break;
            }
        }

        if ($source === null) {
            abort(404);
        }

        // 3) Si ya es JPG, se sirve tal cual (sin re-encodear).
        if (in_array($sourceExt, ['jpg', 'jpeg'], true)) {
            return $this->fileResponse($source);
        }

        // 4) Caché: storage/app/public/_jpg/<base>.jpg. Se reusa si está al día.
        $cacheRel = '_jpg/' . $base . '.jpg';
        $cachePath = storage_path('app/public/' . $cacheRel);
        if (is_file($cachePath) && filemtime($cachePath) >= filemtime($source)) {
            return $this->fileResponse($cachePath);
        }

        // 5) Convertir a JPG con GD (aplana transparencia sobre fondo blanco).
        $jpg = $this->toJpeg($source, $sourceExt, $cachePath);

        // Si por algún motivo falla la conversión, servimos el original (no 500).
        return $this->fileResponse($jpg ?? $source);
    }

    /** Convierte $source a JPG en $cachePath. Devuelve la ruta o null si falla. */
    private function toJpeg(string $source, string $ext, string $cachePath): ?string
    {
        if (! function_exists('imagecreatefromwebp') || ! function_exists('imagejpeg')) {
            return null; // GD sin soporte; se sirve el original.
        }

        try {
            $img = match ($ext) {
                'webp' => @imagecreatefromwebp($source),
                'png'  => @imagecreatefrompng($source),
                'gif'  => @imagecreatefromgif($source),
                default => false,
            };
            if ($img === false) {
                return null;
            }

            $w = imagesx($img);
            $h = imagesy($img);
            // Lienzo blanco para aplanar PNG/WebP con transparencia.
            $canvas = imagecreatetruecolor($w, $h);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, 0, 0, $w, $h, $white);
            imagecopy($canvas, $img, 0, 0, 0, 0, $w, $h);

            $dir = dirname($cachePath);
            if (! is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }

            $ok = imagejpeg($canvas, $cachePath, 85);
            imagedestroy($img);
            imagedestroy($canvas);

            return $ok ? $cachePath : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function fileResponse(string $file): BinaryFileResponse
    {
        return response()->file($file, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
