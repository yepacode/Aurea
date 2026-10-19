<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Enriquece el `description` (cuerpo visible de la ficha, NO la
 * meta_description) de los productos con contenido delgado.
 *
 * Motivo: Google Search Console reporta cientos de URLs en "Descubierta:
 * actualmente sin indexar". Una de las causas es contenido delgado: fichas
 * cuyo cuerpo es el nombre del producto repetido o una frase truncada.
 *
 * El texto generado se arma SÓLO con datos verificables del propio producto
 * (nombre, categoría, marca, presentación extraída del nombre) más vocabulario
 * de oficio por familia de categoría. No inventa ingredientes, certificaciones,
 * origen, propiedades médicas ni tiempos de entrega.
 *
 * Determinismo: las variantes se eligen con `array_rand` sembrado por el id del
 * producto, así dos corridas producen exactamente el mismo texto y dos
 * productos de la misma categoría producen textos distintos.
 *
 * Uso:
 *   php artisan products:enrich-descriptions --dry-run --limit=5
 *   php artisan products:enrich-descriptions --min=150
 */
class ProductsEnrichDescriptions extends Command
{
    protected $signature = 'products:enrich-descriptions
        {--dry-run : mostrar los textos generados sin guardar}
        {--min=150 : umbral de caracteres; por debajo de esto se considera contenido delgado}
        {--limit=0 : procesar solo N productos (0 = todos)}';

    protected $description = 'Genera contenido real (150-350 palabras) para el cuerpo de la ficha de los productos con descripción delgada, con plantillas por familia de categoría.';

    /** Etiquetas permitidas en el cuerpo. */
    private const ALLOWED_TAGS = '<p><br><ul><ol><li><strong><em><b><i>';

    /** slug de categoría => familia de plantillas. */
    private array $familyBySlug = [
        'unas'                         => 'unas',
        'unas-modernas'                => 'unas',
        'esmaltes'                     => 'unas',
        'preparadores-y-finalizadores' => 'preparadores',
        'construccion-y-esculpido'     => 'construccion',
        'remocion-y-mantenimiento'     => 'remocion',
        'brocas'                       => 'brocas',
        'limas'                        => 'limas',
        'herramientas-manuales'        => 'herramientas',
        'tijeras'                      => 'herramientas',
        'pinceleria'                   => 'pinceleria',
        'equipos-electricos'           => 'equipos',
        'decoracion'                   => 'decoracion',
        'bledos'                       => 'decoracion',
        'rostro-y-piel'                => 'rostro',
        'pestanas'                     => 'pestanas',
        'peluqueria'                   => 'peluqueria',
        'cepillos'                     => 'peluqueria',
        'gorros'                       => 'peluqueria',
        'depilacion'                   => 'depilacion',
        'seguridad-e-higiene'          => 'higiene',
        'maquillaje'                   => 'maquillaje',
        'combos'                       => 'default',
    ];

    public function handle(): int
    {
        $dry   = (bool) $this->option('dry-run');
        $min   = max(1, (int) $this->option('min'));
        $limit = max(0, (int) $this->option('limit'));

        // Se traen los ids primero: el WHERE se apoya en la misma columna que
        // vamos a escribir, así que chunkById se comería productos a mitad de
        // camino.
        $query = Product::active()
            ->whereRaw('CHAR_LENGTH(COALESCE(description, "")) < ?', [$min])
            ->orderBy('id');

        $ids = $query->pluck('id')->all();

        if ($limit > 0) {
            $ids = array_slice($ids, 0, $limit);
        }

        $total = count($ids);

        $this->info("Productos con descripción delgada (< {$min} chars): {$total}" . ($dry ? ' — DRY RUN' : ''));

        if ($total === 0) {
            return self::SUCCESS;
        }

        $updated   = 0;
        $skipped   = 0;
        $lenSum    = 0;
        $wordsMin  = PHP_INT_MAX;
        $wordsMax  = 0;

        foreach (array_chunk($ids, 100) as $chunkIds) {
            $products = Product::with(['category', 'brand'])->whereIn('id', $chunkIds)->get();

            foreach ($products as $p) {
                $html = $this->buildDescription($p);

                if ($html === null) {
                    $skipped++;
                    continue;
                }

                $words    = $this->countWords($html);
                $wordsMin = min($wordsMin, $words);
                $wordsMax = max($wordsMax, $words);
                $lenSum  += mb_strlen($html);

                if ($dry) {
                    $this->line('');
                    $this->line("<fg=yellow>──────────────────────────────────────────────────────────</>");
                    $this->line("<fg=cyan>[{$p->id}] {$p->name}</>  (cat: " . (optional($p->category)->slug ?? 'sin-categoria')
                        . " · familia: " . $this->familyFor($p)
                        . " · antes: " . mb_strlen((string) $p->description) . " chars"
                        . " · ahora: " . mb_strlen($html) . " chars / {$words} palabras)");
                    $this->line($html);
                } else {
                    $p->forceFill(['description' => $html])->save();
                }

                $updated++;
            }
        }

        $this->line('');
        $this->info(($dry ? 'Se generarían' : 'Actualizados') . ": {$updated}   ·   Omitidos: {$skipped}");

        if ($updated > 0) {
            $this->info('Longitud promedio generada: ' . round($lenSum / $updated) . " chars   ·   palabras: {$wordsMin}–{$wordsMax}");
        }

        return self::SUCCESS;
    }

    // ───────────────────────────── generación ─────────────────────────────

    private function buildDescription(Product $p): ?string
    {
        $name = trim((string) $p->name);

        if ($name === '') {
            return null;
        }

        $family   = $this->familyFor($p);
        $cfg      = $this->family($family);
        $catName  = trim((string) (optional($p->category)->name ?? ''));
        $brand    = trim((string) (optional($p->brand)->name ?? ''));
        $pres     = $this->presentation($name);
        $seed     = $this->usableSeed($p, $name);

        $repl = [
            ':name'  => $name,
            ':cat'   => $catName !== '' ? mb_strtolower($catName) : $cfg['label'],
            ':label' => $cfg['label'],
            ':item'  => $cfg['item'],
        ];

        // 1) Apertura (40-60 palabras).
        $parts = [];

        if ($seed !== null) {
            // Hay texto real heredado del catálogo: se respeta como primera
            // frase y se le agrega contexto, en vez de botarlo.
            $parts[] = $seed;
            $parts[] = $this->tpl($this->pick($cfg['bridges'], $p->id, 11), $repl);
        } else {
            $parts[] = $this->tpl($this->pick($cfg['openers'], $p->id, 13), $repl);
        }

        $parts[] = $this->tpl($this->pick($cfg['audience'], $p->id, 17), $repl);

        $opening = $this->sentence(implode(' ', array_filter($parts)));

        // 2) Lista de características, derivadas de datos reales.
        $bullets = [];

        if ($catName !== '') {
            $bullets[] = 'Categoría: ' . $catName . ' en el catálogo de Belleza Áurea.';
        }

        if ($pres !== null) {
            $bullets[] = 'Presentación: ' . $pres . ', tal como viene referenciada en el nombre del producto.';
        }

        if ($brand !== '') {
            $bullets[] = 'Marca: ' . $brand . '.';
        }

        // Se completan de 3 a 5 viñetas con frases de oficio de la familia.
        // La cantidad también varía por producto para que dos fichas vecinas
        // de la misma categoría no queden con la misma estructura.
        $real  = count($bullets);
        $need  = max(3 - $real, 2 + ($p->id % 2));
        $need  = min($need, 5 - $real, count($cfg['features']));
        $extra = $this->pickMany($cfg['features'], $p->id, 19, max($need, 0));

        foreach ($extra as $f) {
            if (count($bullets) >= 5) {
                break;
            }
            $bullets[] = $this->sentence($this->tpl($f, $repl));
        }

        // 3) Párrafo de uso. Se arma con el bloque elegido más una
        // recomendación suelta tomada de otro bloque de la misma familia:
        // así dos productos vecinos no repiten el mismo párrafo completo.
        $usageRaw = $this->pick($cfg['usage'], $p->id, 23);
        $tip      = $this->extraTip($cfg['usage'], $usageRaw, $p->id, 31, $repl);
        $usageRaw .= $tip !== null ? ' ' . $tip : '';
        $usage    = $this->sentence($this->tpl($usageRaw, $repl));

        // 4) Cierre de marca (30-40 palabras).
        $closing = $this->sentence($this->tpl($this->pick($this->closers(), $p->id, 29), $repl));

        // Piso de 155 palabras: si quedó corto se suma otra recomendación de
        // la misma familia antes de armar el HTML.
        $compose = fn (string $u) => '<p>' . e($opening) . '</p>' . "\n"
            . '<ul>' . "\n"
            . implode("\n", array_map(fn ($b) => '    <li>' . e($b) . '</li>', $bullets)) . "\n"
            . '</ul>' . "\n"
            . '<p>' . e($u) . '</p>' . "\n"
            . '<p>' . e($closing) . '</p>';

        $salt = 37;

        while ($this->countWords($compose($usage)) < 155 && $salt < 60) {
            $more = $this->extraTip($cfg['usage'], $usageRaw, $p->id, $salt, $repl);
            $salt += 6;

            if ($more === null) {
                break;
            }

            $usageRaw .= ' ' . $more;
            $usage = $this->sentence($this->tpl($usageRaw, $repl));
        }

        return $compose($usage);
    }

    /**
     * Texto previo aprovechable: devuelve la descripción heredada cuando es
     * una frase real (no el nombre repetido) y la limpia del truncado "...".
     */
    private function usableSeed(Product $p, string $name): ?string
    {
        $raw = trim(strip_tags((string) $p->description));

        if ($raw === '') {
            return null;
        }

        // Quitar el truncado del importador y cortar en la última frase completa.
        $raw = trim(preg_replace('/\s*(\.{2,}|…)\s*$/u', '', $raw));

        if (mb_strlen($raw) < 45) {
            return null;
        }

        // Si es el nombre (o casi) repetido, no aporta nada.
        $normSeed = Str::lower(Str::ascii($raw));
        $normName = Str::lower(Str::ascii($name));

        if ($normSeed === $normName || Str::contains($normName, $normSeed)) {
            return null;
        }

        // Cortar en el último signo de cierre para no dejar frases a medias.
        $stops = [];
        foreach (['.', '!', '?'] as $sep) {
            $pos = mb_strrpos($raw, $sep);
            if ($pos !== false) {
                $stops[] = $pos;
            }
        }

        $lastStop = $stops ? max($stops) : -1;

        if ($lastStop >= 40) {
            $raw = mb_substr($raw, 0, $lastStop + 1);
        }

        return $this->sentence($raw);
    }

    /**
     * Presentación real, leída del propio nombre del producto.
     * Devuelve null cuando el nombre no declara ninguna.
     */
    private function presentation(string $name): ?string
    {
        $n = preg_replace('/\s+/', ' ', $name);

        // "x 15 ml", "x15ml", "x 1000ml", "x 120 ml"
        if (preg_match('/\bx\s*(\d+(?:[.,]\d+)?)\s*(ml|l|lt|cc)\b/iu', $n, $m)) {
            return $this->num($m[1]) . ' ' . $this->unit($m[2]);
        }

        // "130 ml", "30 ml" sin la x
        if (preg_match('/\b(\d+(?:[.,]\d+)?)\s*(ml|cc)\b/iu', $n, $m)) {
            return $this->num($m[1]) . ' ' . $this->unit($m[2]);
        }

        // "x100 Gramos", "x 60 g"
        if (preg_match('/\bx\s*(\d+(?:[.,]\d+)?)\s*(gramos|gr|g|kg)\b/iu', $n, $m)) {
            return $this->num($m[1]) . ' ' . $this->unit($m[2]);
        }

        // "x 100 Uds", "x15 U", "x240 unidades"
        if (preg_match('/\bx\s*(\d+)\s*(uds?|unidades|unds?|u|piezas|pzas)\b/iu', $n, $m)) {
            return 'paquete por ' . $m[1] . ' unidades';
        }

        // Granulado de lima: "100/180"
        if (preg_match('/\b(\d{2,4})\s*\/\s*(\d{2,4})\b/', $n, $m)) {
            return 'granulado ' . $m[1] . '/' . $m[2];
        }

        // Potencia: "120 Watts", "24W"
        if (preg_match('/\b(\d+)\s*(watts?|w)\b/iu', $n, $m)) {
            return $m[1] . ' watts de potencia';
        }

        // "x100", "X3", "x2" sin unidad explícita
        if (preg_match('/\bx\s*(\d+)\b/iu', $n, $m)) {
            return 'presentación por ' . $m[1] . ' unidades';
        }

        return null;
    }

    private function num(string $raw): string
    {
        return str_replace(',', '.', $raw);
    }

    private function unit(string $u): string
    {
        $u = Str::lower($u);

        return match ($u) {
            'l', 'lt'            => 'litros',
            'cc'                 => 'cc',
            'gramos', 'gr', 'g'  => 'gramos',
            'kg'                 => 'kilogramos',
            default              => 'ml',
        };
    }

    // ───────────────────────────── utilidades ─────────────────────────────

    private function familyFor(Product $p): string
    {
        $slug = (string) (optional($p->category)->slug ?? '');

        return $this->familyBySlug[$slug] ?? 'default';
    }

    /**
     * Toma una recomendación suelta de otro bloque de uso de la misma familia.
     * Se descartan las frases demasiado cortas y la que ya está en el párrafo.
     */
    private function extraTip(array $usagePool, string $chosen, int $id, int $salt, array $repl): ?string
    {
        $tips = [];

        foreach ($usagePool as $block) {
            $sentences = preg_split('/(?<=[.!?])\s+/u', $block, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            // La primera frase de cada bloque es la que abre el párrafo; se
            // descarta para no terminar con dos aperturas seguidas.
            foreach (array_slice($sentences, 1) as $s) {
                $s = trim($s);
                if (mb_strlen($s) >= 70 && mb_strlen($s) <= 220) {
                    $tips[] = $s;
                }
            }
        }

        $tips = array_values(array_unique($tips));
        $tips = array_values(array_filter($tips, fn ($s) => ! str_contains($chosen, $s)));

        if ($tips === []) {
            return null;
        }

        return $this->tpl($this->pick($tips, $id, $salt), $repl);
    }

    /** Conteo de palabras seguro en UTF-8 (str_word_count es byte-based). */
    private function countWords(string $html): int
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');
        $words = preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        return is_array($words) ? count($words) : 0;
    }

    private function tpl(string $text, array $repl): string
    {
        return strtr($text, $repl);
    }

    private function sentence(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if ($text === '') {
            return $text;
        }

        if (! preg_match('/[.!?»)]$/u', $text)) {
            $text .= '.';
        }

        return $text;
    }

    /**
     * Elección determinista: misma semilla => mismo texto en cada corrida.
     *
     * Ojo con la semilla: `mt_srand()` con semillas que avanzan de forma
     * pareja (id * k) devuelve primeros sorteos casi monótonos, así que ids
     * consecutivos terminaban cayendo en la misma variante. Por eso la
     * semilla pasa por `crc32`, que sí mezcla.
     *
     * Además el índice se rota con el propio id dentro de bloques de `n`
     * productos: dentro de cada bloque se usan las n variantes una sola vez,
     * de modo que dos productos con ids seguidos —que casi siempre son de la
     * misma categoría, porque así se importó el catálogo— nunca reciben el
     * mismo texto.
     */
    private function pick(array $pool, int $id, int $salt): string
    {
        $pool = array_values($pool);
        $n    = count($pool);

        if ($n === 1) {
            return $pool[0];
        }

        // Una rotación distinta por bloque de n ids consecutivos.
        mt_srand($this->seed(intdiv($id, $n), $salt));
        $offset = (int) array_rand($pool);

        return $pool[($id + $offset) % $n];
    }

    /** N elementos distintos del pool, también deterministas. */
    private function pickMany(array $pool, int $id, int $salt, int $n): array
    {
        $pool  = array_values($pool);
        $total = count($pool);
        $n     = max(0, min($n, $total));

        if ($n === 0) {
            return [];
        }

        mt_srand($this->seed($id, $salt));
        $keys = (array) array_rand($pool, $n);

        // La rotación es una biyección sobre el pool, así que las n viñetas
        // siguen siendo distintas entre sí.
        return array_map(fn ($k) => $pool[((int) $k + $id) % $total], $keys);
    }

    private function seed(int $n, int $salt): int
    {
        return (int) crc32($salt . ':' . $n);
    }

    // ───────────────────── pools de cierre (marca Áurea) ─────────────────────

    private function closers(): array
    {
        return [
            'En Belleza Áurea despachamos a toda Colombia y acompañamos cada compra por WhatsApp, así resuelves dudas de referencia o cantidades antes de pedir. Si es tu primera compra, tienes descuento de bienvenida disponible en el carrito.',
            'Hacemos envíos a todo el país y puedes escribirnos por WhatsApp si necesitas asesoría para elegir la referencia correcta. Tu primera compra en Belleza Áurea cuenta con descuento de bienvenida que aplicas directamente al pagar.',
            'Belleza Áurea envía a toda Colombia. Si tienes dudas sobre la presentación o quieres armar un pedido más grande, escríbenos por WhatsApp y te orientamos. Recuerda que la primera compra lleva descuento de bienvenida.',
            'Llegamos a toda Colombia con despacho desde nuestra bodega y atención directa por WhatsApp para resolver cualquier duda antes o después de tu pedido. El descuento de bienvenida te espera en tu primera compra.',
            'Puedes pedirlo desde cualquier ciudad del país: enviamos a toda Colombia y atendemos por WhatsApp para asesorarte con referencias, cantidades o reposición de inventario. Tu primera compra tiene descuento de bienvenida.',
            'Somos una marca de cosmética consciente que despacha a toda Colombia y responde por WhatsApp cuando necesitas apoyo para elegir. Si todavía no has comprado con nosotras, el descuento de bienvenida aplica en tu primer pedido.',
            'Despachamos a toda Colombia y te acompañamos por WhatsApp durante todo el proceso, desde la elección hasta la entrega. En Belleza Áurea tu primera compra siempre tiene descuento de bienvenida.',
            'Envíos a toda Colombia y asesoría real por WhatsApp con alguien del equipo, no con un robot. Si es tu primer pedido en Belleza Áurea, aprovecha el descuento de bienvenida antes de finalizar la compra.',
        ];
    }

    // ───────────────────── plantillas por familia ─────────────────────

    private function family(string $key): array
    {
        $families = $this->families();

        return $families[$key] ?? $families['default'];
    }

    private function families(): array
    {
        return [

            // ── Uñas: esmaltes, semipermanentes, tips ──────────────────────
            'unas' => [
                'label' => 'uñas',
                'item'  => 'producto de uñas',
                'openers' => [
                    ':name es una referencia de la línea de uñas de Belleza Áurea, pensada para quienes trabajan el esmaltado y la manicura con criterio profesional.',
                    'Conoce :name, parte de nuestra selección de uñas, elegida para manicuristas que buscan un acabado parejo y un producto que responda en el día a día del salón.',
                    ':name llega a nuestro catálogo de :cat para resolver una necesidad concreta en la mesa de trabajo: tener a la mano un insumo confiable y fácil de manejar.',
                    'Dentro de la línea de :cat, :name es de las referencias que más piden quienes montan su propio set de manicura, tanto en salón como en casa.',
                    ':name forma parte de nuestra propuesta de uñas: productos escogidos uno por uno, pensando en el rendimiento real y no solo en la presentación.',
                    'Te presentamos :name, una opción de :cat para quienes ya tienen su técnica definida y necesitan insumos que acompañen ese nivel de trabajo.',
                    ':name hace parte del surtido de uñas de Belleza Áurea, armado con lo que de verdad usa una manicurista en jornada completa.',
                ],
                'bridges' => [
                    'Hace parte de nuestra línea de :cat y lo encuentras disponible con despacho desde nuestro inventario.',
                    'Está dentro de la categoría de :cat de Belleza Áurea, junto al resto de insumos de la mesa de manicura.',
                    'Lo tenemos clasificado en :cat para que lo encuentres rápido cuando estés armando tu pedido.',
                    'Pertenece a nuestra selección de :cat, pensada para trabajo profesional y uso frecuente.',
                    'Forma parte de la línea de :cat que manejamos en Belleza Áurea de forma permanente.',
                ],
                'audience' => [
                    'Es una referencia que funciona tanto para manicuristas con salón propio como para quien está montando su kit personal.',
                    'Sirve para profesionales en ejercicio y también para quien apenas empieza y quiere trabajar con insumos serios.',
                    'La piden mucho quienes atienden clientas a diario y necesitan reponer inventario sin cambiar de proveedor.',
                    'Es apropiada tanto para uso profesional como para quien cuida sus uñas en casa con buenos materiales.',
                    'La usan tanto en salones de uñas como estudiantes de manicura que están armando su maletín de trabajo.',
                ],
                'features' => [
                    'Clasificado dentro de la línea de uñas, junto al resto de insumos de manicura del catálogo.',
                    'Disponible para compra por unidad y también para pedidos al por mayor.',
                    'Pensado para rotación constante en mesa de trabajo profesional.',
                    'Compatible con el flujo habitual de manicura: preparación, aplicación y sellado.',
                    'Se puede combinar con otras referencias de la misma categoría para armar un set completo.',
                    'Se guarda bien en estuche o cajón de mesa de manicura sin ocupar mucho espacio.',
                ],
                'usage' => [
                    'Trabájalo sobre la uña ya preparada: limpia, libre de residuos de producto anterior y con la cutícula atendida. Esa preparación previa es la que define qué tan parejo queda el resultado y cuánto dura el trabajo sobre la uña.',
                    'Úsalo dentro de tu secuencia habitual de manicura, respetando el orden de preparación, aplicación y sellado. Mantén la superficie de trabajo limpia y cierra bien el envase después de cada uso para que el producto conserve su textura.',
                    'Intégralo al paso que te corresponda del servicio, siempre sobre uña seca y desengrasada. Aplica en capas delgadas antes que en una sola capa gruesa: así se controla mejor el resultado y se evita acumulación en los bordes.',
                    'Incorpóralo en el momento del ritual de manicura donde lo necesites, cuidando que la uña esté previamente limada y libre de brillo natural. Guárdalo lejos del sol directo y de fuentes de calor para que mantenga sus propiedades.',
                    'Empléalo siguiendo tu técnica de siempre, sobre una base bien preparada. En servicio profesional conviene tener la referencia duplicada en el maletín, porque es de las que más se consumen en jornadas con agenda llena.',
                    'Aplícalo en la etapa correspondiente del servicio y deja que cada capa haga su trabajo antes de pasar a la siguiente. Revisa el estado de la uña antes de empezar: un buen diagnóstico ahorra retoques después.',
                ],
            ],

            // ── Preparadores y finalizadores ───────────────────────────────
            'preparadores' => [
                'label' => 'preparadores y finalizadores',
                'item'  => 'preparador o finalizador',
                'openers' => [
                    ':name pertenece a la línea de preparadores y finalizadores: los productos que abren y cierran el servicio de manicura, y de los que depende buena parte de la duración del trabajo.',
                    'En la mesa de manicura, el primer y el último paso son los que sostienen todo lo demás. :name es parte de esa línea de :cat dentro del catálogo de Belleza Áurea.',
                    ':name entra en la categoría de :cat, donde reunimos lo que se aplica antes del color y lo que lo sella al final del servicio.',
                    'Conoce :name, una referencia de :cat para quienes saben que un esmaltado bien preparado y bien sellado es lo que marca la diferencia.',
                    ':name está en nuestra selección de :cat, la línea que suele ser la primera en acabarse cuando el salón tiene buen movimiento.',
                    'Sumamos :name a la línea de :cat porque responde al paso donde más se nota la técnica: el principio y el cierre del esmaltado.',
                ],
                'bridges' => [
                    'Está clasificado en :cat, la línea de apertura y cierre del servicio.',
                    'Lo encuentras en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de la línea de :cat que manejamos de forma permanente.',
                    'Pertenece a :cat, junto al resto de productos de preparación y sellado.',
                    'Está dentro de nuestra selección de :cat para trabajo profesional.',
                ],
                'audience' => [
                    'Es de uso diario en cualquier salón de uñas y también en el kit de quien trabaja a domicilio.',
                    'Lo piden sobre todo manicuristas con agenda constante, porque es un insumo de consumo permanente.',
                    'Funciona igual de bien en servicio profesional que en manicura en casa cuando se quiere un acabado duradero.',
                    'Es una compra recurrente para quienes atienden varias clientas al día y no pueden quedarse sin existencias.',
                    'Lo usan tanto técnicas con experiencia como estudiantes que están aprendiendo a controlar la duración del esmaltado.',
                ],
                'features' => [
                    'Corresponde al paso de preparación o sellado dentro del servicio de manicura.',
                    'Se usa en combinación con el resto de la línea de esmaltado semipermanente.',
                    'Pensado para rotación alta: es de los insumos que más se consumen en salón.',
                    'Formato pensado para la mesa de trabajo, cómodo de manipular con una sola mano.',
                    'Disponible por unidad y también para reposición de inventario al por mayor.',
                    'Se conserva mejor en lugar fresco y con el envase bien cerrado.',
                ],
                'usage' => [
                    'Aplícalo sobre la uña completamente limpia y seca, sin restos de aceite ni de producto anterior. Si corresponde al paso de sellado, cúbrelo hasta el borde libre y cura según el tiempo que indique tu lámpara. La constancia en este paso es lo que sostiene la duración del servicio.',
                    'Úsalo en el momento exacto de la secuencia: preparación antes del color, finalizador después. Trabaja en capas delgadas y uniformes, sin desbordar hacia la cutícula, porque ese desborde es la causa más común de que el esmaltado se levante antes de tiempo.',
                    'Trabájalo sobre una uña previamente limada y desengrasada. Distribuye de manera pareja desde la base hasta el borde y sella bien la punta. Mantén el envase cerrado cuando no lo estés usando para conservar la consistencia del producto.',
                    'Incorpóralo al inicio o al cierre del servicio según corresponda, siempre con pincel limpio. Dos capas finas rinden mejor que una gruesa: controlas el acabado y evitas que el producto se acumule en los laterales de la uña.',
                    'Empléalo como parte fija de tu protocolo de esmaltado. Revisa que la superficie esté libre de brillo natural antes de aplicar y respeta los tiempos de curado de tu equipo; saltarse ese tiempo es lo que más acorta la duración del trabajo.',
                    'Aplícalo con movimientos cortos y controlados, cuidando el margen con la piel. Si trabajas en salón, tenlo siempre al alcance en la mesa: es el paso que no se puede improvisar ni reemplazar por otro producto.',
                ],
            ],

            // ── Construcción y esculpido ───────────────────────────────────
            'construccion' => [
                'label' => 'construcción y esculpido',
                'item'  => 'insumo de construcción',
                'openers' => [
                    ':name hace parte de la línea de construcción y esculpido: el grupo de insumos con los que se levanta estructura sobre la uña natural o sobre tip.',
                    'Para construir uña se necesita material que responda bajo presión y que permita trabajar sin afanes. :name es parte de nuestra línea de :cat con ese propósito.',
                    ':name está en la categoría de :cat de Belleza Áurea, donde reunimos lo necesario para esculpido, extensión y reconstrucción.',
                    'Conoce :name, una referencia de :cat pensada para técnicas que trabajan estructura, no solo color.',
                    ':name entra en la línea de :cat, la que usan las manicuristas que ofrecen servicio de uña esculpida y mantenimiento.',
                    'Sumamos :name a nuestra selección de :cat porque es de los insumos que sostienen el trabajo de extensión y relleno.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de la línea de :cat para trabajo de estructura y extensión.',
                    'Pertenece a :cat, junto al resto de materiales de esculpido.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra línea de :cat de uso profesional.',
                ],
                'audience' => [
                    'Está dirigido a manicuristas que ofrecen esculpido, extensión o reconstrucción como parte de su servicio.',
                    'Lo usan técnicas con práctica en estructura, que ya controlan el manejo del producto sobre la uña.',
                    'Es un insumo de trabajo profesional, de los que se reponen seguido cuando hay agenda llena.',
                    'Funciona para quien hace mantenimiento quincenal y necesita material consistente en cada servicio.',
                    'Lo piden tanto salones especializados en uña esculpida como técnicas independientes a domicilio.',
                ],
                'features' => [
                    'Corresponde a la etapa de construcción o esculpido dentro del servicio de uñas.',
                    'Se trabaja junto con pinceles, limas y brocas de la misma línea del catálogo.',
                    'Pensado para uso profesional con rotación frecuente en el salón.',
                    'Formato de mesa de trabajo, para tenerlo al alcance durante todo el servicio.',
                    'Disponible por unidad y para reposición de inventario al por mayor.',
                    'Conviene guardarlo cerrado, en lugar fresco y lejos de la luz directa.',
                ],
                'usage' => [
                    'Trabájalo sobre la uña ya preparada y con el molde o tip bien posicionado. Avanza por zonas, sin cargar demasiado material de una sola vez, y deja que cada capa quede definida antes de seguir. La estructura se construye por etapas, no de un solo golpe.',
                    'Úsalo con el pincel adecuado y mantén el producto a temperatura estable. Controla la cantidad que tomas en cada pasada: el exceso se acumula en el área de la cutícula y obliga a limar de más en el acabado, lo que desgasta tiempo de servicio.',
                    'Aplícalo cuando la uña esté limada, sin brillo y libre de polvo. Reparte el material respetando el arco y el punto de estrés de la uña, que es donde se define la resistencia real del trabajo terminado.',
                    'Incorpóralo en la etapa de construcción, después de la preparación y antes del acabado. Ventila el espacio de trabajo y mantén la mesa despejada: en esculpido, el orden en la mesa se nota en el resultado final.',
                    'Trabájalo con calma y en capas controladas. Si estás haciendo mantenimiento, retira primero el producto levantado antes de aplicar material nuevo; eso evita que queden espacios entre capas que luego se levantan.',
                    'Empléalo dentro de tu protocolo de esculpido habitual, con herramienta limpia en cada clienta. Cierra bien el envase apenas termines, porque este tipo de material es sensible a quedar destapado durante la jornada.',
                ],
            ],

            // ── Remoción y mantenimiento ───────────────────────────────────
            'remocion' => [
                'label' => 'remoción y mantenimiento',
                'item'  => 'producto de remoción',
                'openers' => [
                    ':name pertenece a la línea de remoción y mantenimiento, la parte del servicio que casi nadie muestra pero que define la salud de la uña a largo plazo.',
                    'Retirar bien es tan importante como aplicar bien. :name hace parte de nuestra línea de :cat con ese enfoque.',
                    ':name está en la categoría de :cat de Belleza Áurea, pensada para el retiro controlado de producto y el cuidado entre servicios.',
                    'Conoce :name, una referencia de :cat para quienes prefieren retirar con producto y no a punta de lima.',
                    ':name entra en la línea de :cat, la que se usa al comenzar el servicio cuando la clienta llega con trabajo anterior.',
                    'Sumamos :name a nuestra selección de :cat porque el retiro mal hecho es la causa más común de uña debilitada.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de la línea de :cat para el retiro y cuidado entre servicios.',
                    'Pertenece a :cat, junto al resto de productos de mantenimiento.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra línea de :cat de uso profesional.',
                ],
                'audience' => [
                    'Es de uso permanente en salón, porque casi todo servicio empieza retirando el trabajo anterior.',
                    'Lo usan manicuristas que cuidan la uña natural de sus clientas entre un servicio y otro.',
                    'Funciona tanto en salón como en casa para quien quiere retirar sin maltratar la lámina ungueal.',
                    'Lo piden sobre todo quienes atienden clientas frecuentes y hacen mantenimiento cada dos o tres semanas.',
                    'Es apropiado para profesionales y también para uso personal con el cuidado debido.',
                ],
                'features' => [
                    'Corresponde a la etapa de retiro o mantenimiento dentro del servicio de uñas.',
                    'Se usa junto con papel aluminio, algodón o los accesorios de remoción del catálogo.',
                    'Insumo de consumo frecuente en salón: conviene tenerlo siempre en inventario.',
                    'Disponible por unidad y también en presentaciones para reposición al por mayor.',
                    'Debe guardarse bien cerrado, en lugar fresco y lejos de fuentes de calor.',
                    'Pensado para trabajo controlado, sin forzar ni raspar la uña natural.',
                ],
                'usage' => [
                    'Úsalo al inicio del servicio, sobre el producto que vas a retirar, y dale el tiempo que necesita para actuar. No fuerces ni levantes con la herramienta antes de tiempo: ahí es donde se maltrata la lámina natural y la clienta termina con la uña delgada.',
                    'Aplícalo siguiendo tu protocolo de retiro habitual y respeta los tiempos. Retira con movimiento suave y, si queda producto adherido, repite el paso en vez de raspar. La paciencia en este punto es lo que mantiene la uña sana entre servicios.',
                    'Trabájalo con el área ventilada y protegiendo la piel de alrededor. Limita el contacto al producto que quieres retirar y, al terminar, hidrata la cutícula para devolverle confort a la zona después del proceso.',
                    'Incorpóralo como primer paso cuando la clienta llega con trabajo anterior. Evalúa el estado de la uña antes de empezar y define si corresponde retiro total o solo mantenimiento; no todos los servicios necesitan retirar todo.',
                    'Empléalo con el tiempo de acción adecuado y sin apuros. Mantén el envase cerrado durante la jornada y usa solo la cantidad necesaria: el exceso no acelera el proceso y sí reseca más la piel alrededor de la uña.',
                    'Úsalo al comienzo del servicio y termina siempre con un paso de hidratación. Si trabajas en salón, tenlo cerca de la mesa de retiro para no interrumpir el flujo de atención entre una clienta y otra.',
                ],
            ],

            // ── Brocas ─────────────────────────────────────────────────────
            'brocas' => [
                'label' => 'brocas',
                'item'  => 'broca',
                'openers' => [
                    ':name hace parte de nuestra línea de brocas para torno de manicure, la herramienta que define qué tan limpio queda el trabajo alrededor de la cutícula.',
                    'Una broca mal elegida se nota de inmediato en la uña. :name es parte de nuestra selección de :cat para evitar justamente eso.',
                    ':name está en la categoría de :cat de Belleza Áurea, pensada para quienes trabajan con torno y necesitan precisión en cada pasada.',
                    'Conoce :name, una referencia de :cat para técnicas que ya manejan torno y saben lo que buscan en una punta.',
                    ':name entra en nuestra línea de :cat, la que se usa en preparación, mantenimiento y acabado de la uña.',
                    'Sumamos :name al surtido de :cat porque tener la punta correcta a la mano ahorra tiempo en cada servicio.',
                ],
                'bridges' => [
                    'Está clasificada en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para torno de manicure.',
                    'Pertenece a :cat, junto al resto de puntas y accesorios de torno.',
                    'La encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso profesional.',
                ],
                'audience' => [
                    'Está dirigida a manicuristas que trabajan con torno eléctrico en servicio profesional.',
                    'La usan técnicas con práctica en torno, que ya controlan la velocidad y el ángulo de trabajo.',
                    'Es una herramienta de uso frecuente en salones que ofrecen esculpido y mantenimiento.',
                    'La piden quienes hacen retiro y preparación con torno en lugar de lima manual.',
                    'Funciona para profesionales en ejercicio y para quien está ampliando su set de puntas.',
                ],
                'features' => [
                    'Diseñada para uso con torno de manicure de vástago estándar.',
                    'Corresponde a la etapa de preparación, retiro o acabado según la técnica que uses.',
                    'Se complementa con el resto de puntas de la línea para cubrir todo el servicio.',
                    'Debe limpiarse y desinfectarse entre clienta y clienta.',
                    'Disponible por unidad y también para reposición al por mayor.',
                    'Conviene guardarla en estuche o soporte para proteger el filo de la punta.',
                ],
                'usage' => [
                    'Móntala en el torno verificando que quede firme antes de encender. Trabaja a velocidad moderada y con la punta casi paralela a la uña, nunca clavada: el contacto debe ser de roce, no de presión. Así se controla el calor y se evita molestar a la clienta.',
                    'Úsala con movimientos continuos y sin detenerte en un mismo punto. Si sientes que la zona se calienta, levanta la punta y retoma. La velocidad adecuada depende de tu pulso y del material que estés trabajando, no de llevarla siempre al máximo.',
                    'Ajústala bien al mango del torno y prueba el giro antes de acercarla a la uña. Trabaja por zonas cortas y revisa el avance con frecuencia; es preferible dar varias pasadas suaves que intentar resolver todo en una sola.',
                    'Empléala en el paso que corresponda de tu protocolo con torno, siempre con la uña seca. Al terminar el servicio, límpiala y desinféctala antes de guardarla: una punta sucia pierde rendimiento y no debería pasar de una clienta a otra.',
                    'Trabájala con ángulo suave y presión mínima. Mantén la mano apoyada para estabilizar el pulso y respeta el margen con la piel, especialmente en el contorno de la cutícula, que es la zona más delicada del procedimiento.',
                    'Instálala en el torno y empieza a baja velocidad para tomar el punto. Sube solo lo necesario según el trabajo. Revísala periódicamente: cuando una punta pierde eficiencia, obliga a presionar más y ahí es donde aparecen los accidentes.',
                ],
            ],

            // ── Limas ──────────────────────────────────────────────────────
            'limas' => [
                'label' => 'limas',
                'item'  => 'lima',
                'openers' => [
                    ':name hace parte de nuestra línea de limas, la herramienta más básica de la mesa de manicura y también la que más se subestima.',
                    'El limado define la forma y la forma define todo lo demás. :name es parte de nuestra selección de :cat con esa lógica.',
                    ':name está en la categoría de :cat de Belleza Áurea, pensada para dar forma, pulir y preparar la superficie de la uña.',
                    'Conoce :name, una referencia de :cat para quienes trabajan la forma de la uña con precisión y no a la carrera.',
                    ':name entra en nuestra línea de :cat, de esas herramientas que se reponen seguido porque se usan en todos los servicios.',
                    'Sumamos :name al surtido de :cat porque una buena lima cambia por completo la calidad del acabado.',
                ],
                'bridges' => [
                    'Está clasificada en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para mesa de manicura.',
                    'Pertenece a :cat, junto al resto de herramientas de limado y pulido.',
                    'La encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso profesional.',
                ],
                'audience' => [
                    'La usan manicuristas en servicio diario y también quien hace su propia manicura en casa.',
                    'Es apropiada para uso profesional y para quien está armando su kit básico de uñas.',
                    'La piden salones con movimiento alto, porque es un insumo de reposición constante.',
                    'Funciona tanto para técnicas con experiencia como para quien apenas está aprendiendo a dar forma.',
                    'Es parte del maletín de cualquier manicurista que trabaje a domicilio.',
                ],
                'features' => [
                    'Herramienta de limado para dar forma, pulir o preparar la superficie de la uña.',
                    'Se complementa con el resto de limas y bloques pulidores del catálogo.',
                    'Debe limpiarse o reemplazarse según el protocolo de higiene de tu salón.',
                    'Insumo de rotación alta: conviene tener varias en el maletín de trabajo.',
                    'Disponible por unidad y también para reposición de inventario al por mayor.',
                    'Formato de mano, cómodo de manipular durante todo el servicio.',
                ],
                'usage' => [
                    'Límala siempre en una sola dirección, del borde hacia el centro, sin movimientos de vaivén. Ese detalle evita que la uña se abra en capas y es lo que diferencia un limado profesional de uno hecho de afán. Trabaja con la uña seca.',
                    'Úsala con la uña completamente seca y con movimientos firmes pero controlados. Define primero la forma del borde libre y después refina los laterales. Limpia el polvo con cepillo antes de continuar al siguiente paso del servicio.',
                    'Trabájala sin presionar de más: la lima hace el trabajo, no la fuerza de la mano. Revisa la simetría mirando las uñas de frente y de perfil antes de dar por terminado el limado, porque los desniveles se ven apenas se aplica el color.',
                    'Empléala en la etapa de preparación o acabado, según corresponda. Mantén un ángulo constante durante todo el recorrido y haz pausas para revisar el avance. El limado parejo es lo que hace que el esmalte se vea uniforme después.',
                    'Pásala en una sola dirección y con movimientos largos. Si trabajas en salón, ten varias disponibles y aplica tu protocolo de higiene entre clientas; es una herramienta de contacto directo con la uña y la piel.',
                    'Dale forma empezando por los laterales y cerrando en la punta. Evita limar la superficie de la uña natural más de lo necesario y retira el polvo antes de aplicar cualquier producto encima, porque el residuo impide la adherencia.',
                ],
            ],

            // ── Herramientas manuales y tijeras ────────────────────────────
            'herramientas' => [
                'label' => 'herramientas',
                'item'  => 'herramienta',
                'openers' => [
                    ':name hace parte de nuestra línea de herramientas de trabajo, de esas que se compran una vez y acompañan al profesional durante años si se cuidan bien.',
                    'Una herramienta buena se nota desde el primer servicio. :name es parte de nuestra selección de :cat elegida con ese criterio.',
                    ':name está en la categoría de :cat de Belleza Áurea, pensada para quienes trabajan con las manos y necesitan instrumento confiable.',
                    'Conoce :name, una referencia de :cat para el maletín de trabajo de manicuristas, estilistas y esteticistas.',
                    ':name entra en nuestra línea de :cat, la parte del catálogo donde están los instrumentos de uso permanente.',
                    'Sumamos :name al surtido de :cat porque la diferencia entre un servicio rápido y uno torpe suele estar en la herramienta.',
                ],
                'bridges' => [
                    'Está clasificada en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para trabajo profesional.',
                    'Pertenece a :cat, junto al resto de instrumentos del maletín.',
                    'La encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso frecuente.',
                ],
                'audience' => [
                    'Está pensada para profesionales de belleza que trabajan con agenda y necesitan instrumento que responda.',
                    'La usan manicuristas, estilistas y esteticistas según el tipo de servicio que presten.',
                    'Es apropiada para salón, para trabajo a domicilio y para quien está armando su maletín.',
                    'La piden tanto profesionales con años de oficio como estudiantes en formación.',
                    'Funciona para uso profesional continuo y también para cuidado personal en casa.',
                ],
                'features' => [
                    'Instrumento de trabajo manual, para uso directo durante el servicio.',
                    'Requiere limpieza y desinfección entre un cliente y otro.',
                    'Se complementa con el resto de herramientas del catálogo para cubrir el servicio completo.',
                    'Conviene guardarla en estuche para proteger el filo o la punta.',
                    'Disponible por unidad y también para pedidos al por mayor.',
                    'Pensada para uso frecuente en jornada profesional.',
                ],
                'usage' => [
                    'Úsala con movimientos precisos y sin forzar. Si notas que tienes que hacer fuerza, revisa el ángulo de trabajo antes que insistir con presión. Límpiala y desinféctala al terminar cada servicio y guárdala seca para que conserve el filo.',
                    'Trabájala con la mano apoyada para estabilizar el pulso, especialmente cerca de la piel. Al terminar la jornada, límpiala, sécala bien y guárdala en su estuche; la humedad es lo que más daña este tipo de instrumento.',
                    'Empléala en el paso del servicio que corresponda, siempre sobre piel y uña limpias. Aplica tu protocolo de desinfección entre cliente y cliente: en herramienta de contacto directo, la higiene no es opcional.',
                    'Manéjala con movimientos cortos y controlados en lugar de pasadas largas. Revisa su estado antes de cada jornada; una herramienta desgastada obliga a compensar con fuerza y ahí es donde aparecen los cortes y los accidentes.',
                    'Intégrala a tu rutina de trabajo habitual y dale su mantenimiento. Después de usarla, retira los residuos, desinfecta y seca por completo antes de guardar. Ese hábito es lo que estira la vida útil del instrumento.',
                    'Trabájala sobre superficie estable y con buena iluminación. Mantén un ángulo cómodo para la muñeca durante todo el servicio: en jornadas largas, la postura de la mano importa tanto como la calidad de la herramienta.',
                ],
            ],

            // ── Pincelería ─────────────────────────────────────────────────
            'pinceleria' => [
                'label' => 'pincelería',
                'item'  => 'pincel',
                'openers' => [
                    ':name hace parte de nuestra línea de pincelería, el instrumento que traduce la idea en trazo y del que depende por completo el detalle del diseño.',
                    'En nail art el pincel no es un accesorio, es la mano extendida. :name es parte de nuestra selección de :cat con esa importancia.',
                    ':name está en la categoría de :cat de Belleza Áurea, pensada para trabajo de detalle, relleno y decoración sobre la uña.',
                    'Conoce :name, una referencia de :cat para quienes trabajan diseño y necesitan un trazo que responda.',
                    ':name entra en nuestra línea de :cat, donde reunimos puntas y formas para cada tipo de técnica.',
                    'Sumamos :name al surtido de :cat porque cada diseño pide una punta distinta y vale la pena tenerlas todas.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para trabajo de detalle.',
                    'Pertenece a :cat, junto al resto de pinceles y accesorios de diseño.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso profesional.',
                ],
                'audience' => [
                    'Está pensado para manicuristas que ofrecen nail art y diseño personalizado.',
                    'Lo usan técnicas que trabajan detalle fino y necesitan control en el trazo.',
                    'Es apropiado para trabajo profesional y para quien está aprendiendo decoración sobre uña.',
                    'Lo piden quienes arman su set de pinceles por forma y grosor según la técnica.',
                    'Funciona tanto en salón especializado como en el kit de quien trabaja a domicilio.',
                ],
                'features' => [
                    'Instrumento de aplicación y detalle para trabajo sobre la uña.',
                    'Se complementa con el resto de formas y grosores de la línea de pincelería.',
                    'Debe limpiarse después de cada uso para conservar la forma de la punta.',
                    'Conviene guardarlo con la punta protegida y nunca apoyado sobre las cerdas.',
                    'Disponible por unidad y también para pedidos al por mayor.',
                    'Pensado para uso frecuente en servicio profesional de diseño.',
                ],
                'usage' => [
                    'Antes de usarlo, dale forma a la punta y retira cualquier exceso de producto. Trabaja con la mano apoyada y carga poco material en cada pasada: en diseño, es más fácil agregar que corregir. Límpialo apenas termines, sin dejar que el producto seque en las cerdas.',
                    'Úsalo con movimientos continuos y presión mínima. La cantidad de producto en la punta define el grosor del trazo, así que ajusta la carga según el detalle que vayas a hacer. Al terminar, límpialo y déjalo secar en horizontal.',
                    'Trabájalo sobre la uña ya preparada y con buena iluminación. Apoya el meñique en la mesa para estabilizar el pulso: el trazo limpio depende más del apoyo de la mano que de la velocidad con la que dibujes.',
                    'Empléalo en la etapa de decoración del servicio. Ten a mano un paño para limpiar la punta entre colores y evita mezclar productos distintos en el mismo pincel sin limpiarlo; eso arruina tanto el diseño como las cerdas.',
                    'Intégralo a tu set de diseño y resérvalo para el tipo de trabajo que corresponda a su forma. Después de usarlo, retira el residuo, dale forma a la punta con los dedos limpios y guárdalo protegido para la siguiente jornada.',
                    'Carga la punta solo hasta donde necesites y prueba el trazo antes de ir a la uña. Mantén el pincel limpio durante todo el servicio: un pincel con producto endurecido pierde la punta y ya no vuelve a dar el mismo detalle.',
                ],
            ],

            // ── Equipos eléctricos ─────────────────────────────────────────
            'equipos' => [
                'label' => 'equipos eléctricos',
                'item'  => 'equipo',
                'openers' => [
                    ':name hace parte de nuestra línea de equipos eléctricos para salón: la inversión que cambia el ritmo de trabajo de una mesa de manicura.',
                    'Un equipo confiable se paga solo en tiempo de servicio. :name es parte de nuestra selección de :cat con ese criterio.',
                    ':name está en la categoría de :cat de Belleza Áurea, pensada para quienes montan o renuevan su estación de trabajo.',
                    'Conoce :name, una referencia de :cat para profesionales que atienden con agenda y necesitan equipo que aguante la jornada.',
                    ':name entra en nuestra línea de :cat, donde reunimos lo necesario para equipar una mesa de uñas completa.',
                    'Sumamos :name al surtido de :cat porque la estación de trabajo también define la experiencia de la clienta.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para estación de trabajo.',
                    'Pertenece a :cat, junto al resto de equipos y accesorios del salón.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat para montaje de mesa profesional.',
                ],
                'audience' => [
                    'Está pensado para salones y profesionales independientes que están montando o renovando su estación.',
                    'Lo piden quienes atienden varias clientas al día y necesitan equipo que responda en jornada larga.',
                    'Es apropiado para salón establecido y también para quien trabaja a domicilio con equipo portátil.',
                    'Lo usan manicuristas que ya pasaron del kit básico y están equipando en serio.',
                    'Funciona para servicio profesional continuo y para uso personal frecuente.',
                ],
                'features' => [
                    'Equipo de conexión eléctrica para uso en estación de trabajo.',
                    'Se complementa con el resto de accesorios y repuestos del catálogo.',
                    'Conviene revisar el cable y la conexión periódicamente como parte del mantenimiento.',
                    'Pensado para uso continuo en jornada profesional.',
                    'Disponible por unidad y también para pedidos de equipamiento al por mayor.',
                    'Debe guardarse en lugar seco y protegido cuando no esté en uso.',
                ],
                'usage' => [
                    'Conéctalo a una toma en buen estado y revisa el cable antes de cada jornada. Ubícalo en un punto estable de la mesa, donde no estorbe el movimiento de las manos durante el servicio, y desconéctalo al terminar el día.',
                    'Ubícalo en tu estación de forma que quede accesible sin tener que estirarte. Mantén el área despejada y seca alrededor del equipo, y desconéctalo antes de limpiarlo. El mantenimiento básico es lo que estira su vida útil.',
                    'Enciéndelo con tiempo antes de empezar a atender y verifica que funcione correctamente. Limpia la superficie externa al final de la jornada, con el equipo desconectado, y guárdalo protegido del polvo si no lo usas a diario.',
                    'Instálalo en un punto fijo de la mesa, lejos del borde y de líquidos. Revisa periódicamente la conexión y evita sobrecargar la toma con varios equipos a la vez; en salón, ese es el error que más daña los aparatos.',
                    'Úsalo según el flujo de tu servicio, dejándolo listo antes de que llegue la clienta. Desconéctalo al terminar y revisa que no quede con residuos de producto encima, porque eso es lo que con el tiempo afecta el funcionamiento.',
                    'Déjalo montado en su lugar dentro de la estación y manténlo limpio. Antes de cada jornada, una revisión rápida del cable y del encendido evita interrupciones en medio de un servicio con la clienta en la silla.',
                ],
            ],

            // ── Decoración (incluye bledos) ────────────────────────────────
            'decoracion' => [
                'label' => 'decoración',
                'item'  => 'elemento decorativo',
                'openers' => [
                    ':name hace parte de nuestra línea de decoración para uñas, donde está todo lo que convierte un esmaltado correcto en un diseño que la clienta muestra.',
                    'La decoración es lo que la gente fotografía. :name es parte de nuestra selección de :cat pensada justamente para eso.',
                    ':name está en la categoría de :cat de Belleza Áurea, con elementos para armar diseños propios sobre la uña.',
                    'Conoce :name, una referencia de :cat para manicuristas que trabajan diseño y no solo color plano.',
                    ':name entra en nuestra línea de :cat, la parte del catálogo que más crece porque cada temporada pide algo distinto.',
                    'Sumamos :name al surtido de :cat porque tener variedad de apliques abre el abanico de diseños que puedes ofrecer.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para diseño sobre uña.',
                    'Pertenece a :cat, junto al resto de apliques y elementos decorativos.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat para nail art.',
                ],
                'audience' => [
                    'Está pensado para manicuristas que ofrecen nail art y diseños personalizados por temporada.',
                    'Lo usan técnicas que arman su propio surtido de apliques para no repetir diseño entre clientas.',
                    'Es apropiado para trabajo profesional y también para quien decora sus uñas en casa.',
                    'Lo piden salones que quieren diferenciarse ofreciendo diseños que no se ven en todas partes.',
                    'Funciona para servicio profesional y para quien está empezando en decoración de uñas.',
                ],
                'features' => [
                    'Elemento decorativo para aplicar sobre la uña dentro del servicio de nail art.',
                    'Se combina con el resto de apliques y colores de la línea de decoración.',
                    'Requiere un paso de sellado posterior para quedar fijo sobre la uña.',
                    'Conviene guardarlo en organizador con divisiones para no perder piezas.',
                    'Disponible por unidad y también para pedidos al por mayor.',
                    'Permite armar diseños distintos combinándolo con otras referencias del catálogo.',
                ],
                'usage' => [
                    'Aplícalo sobre la capa aún sin curar o con un gel adhesivo, según la técnica que uses, y presiona suavemente con pinza o con el pincel. Después sella con top coat cubriendo bien los bordes, que es donde suele empezar a levantarse el aplique.',
                    'Colócalo con pinza de punta fina para posicionarlo exacto antes de fijar. Una vez ubicado, sella por encima para protegerlo del roce diario. Distribuye el diseño mirando la uña completa, no pieza por pieza, para que quede equilibrado.',
                    'Úsalo en la etapa de decoración, después del color y antes del sellado final. Trabaja con las manos limpias y sobre superficie seca; cualquier residuo de aceite impide que el aplique quede bien adherido a la uña.',
                    'Intégralo al diseño cuando ya tengas definida la base de color. Menos piezas bien ubicadas suelen verse mejor que muchas amontonadas: deja espacio libre en la uña para que la decoración respire y se note.',
                    'Posiciónalo con cuidado y asegúralo con una capa de sellado que lo cubra por completo. Si el aplique tiene relieve, aplica el top coat en dos pasadas delgadas para que quede parejo y no se formen burbujas alrededor.',
                    'Trabájalo sobre la uña preparada y con buena iluminación para ubicar bien cada pieza. Guarda el sobrante en su empaque cerrado: este tipo de material se pierde con facilidad si queda suelto en el cajón de la mesa.',
                ],
            ],

            // ── Rostro y piel ──────────────────────────────────────────────
            'rostro' => [
                'label' => 'rostro y piel',
                'item'  => 'producto de cuidado facial',
                'openers' => [
                    ':name hace parte de nuestra línea de rostro y piel, la sección donde reunimos lo que acompaña una rutina de cuidado facial constante.',
                    'El cuidado de la piel es cuestión de rutina, no de milagros. :name es parte de nuestra selección de :cat con esa idea.',
                    ':name está en la categoría de :cat de Belleza Áurea, pensada para quienes cuidan su piel con productos sencillos y de uso diario.',
                    'Conoce :name, una referencia de :cat para rutinas de cuidado facial en casa y para cabina de estética.',
                    ':name entra en nuestra línea de :cat, donde buscamos opciones de cosmética consciente para el cuidado cotidiano.',
                    'Sumamos :name al surtido de :cat porque una rutina sostenible en el tiempo vale más que un producto aislado.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para rutina de cuidado facial.',
                    'Pertenece a :cat, junto al resto de productos de cuidado de la piel.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de cosmética consciente.',
                ],
                'audience' => [
                    'Es apropiado para uso personal diario y también para cabina de estética.',
                    'Lo usan tanto quienes ya tienen una rutina establecida como quien apenas está empezando a cuidar su piel.',
                    'Lo piden esteticistas para sus servicios y clientas para continuar el cuidado en casa.',
                    'Funciona para rutina de mañana o de noche, según lo que estés buscando complementar.',
                    'Está pensado para quien prefiere productos sencillos antes que rutinas de diez pasos.',
                ],
                'features' => [
                    'Producto de cuidado facial para incorporar a la rutina diaria.',
                    'Se complementa con el resto de la línea de rostro y piel del catálogo.',
                    'Conviene guardarlo cerrado, en lugar fresco y lejos de la luz directa.',
                    'Disponible por unidad y también para pedidos al por mayor.',
                    'Pensado para uso constante: los resultados en piel dependen de la continuidad.',
                    'Formato cómodo para tener en el tocador o llevar en el neceser.',
                ],
                'usage' => [
                    'Úsalo sobre la piel limpia, en el momento de la rutina que te corresponda. Aplica con las manos limpias y da tiempo a que absorba antes de pasar al siguiente paso. Si es tu primera vez con el producto, prueba primero en una zona pequeña.',
                    'Intégralo a tu rutina de mañana o de noche, siempre después de limpiar el rostro. La regularidad es lo que hace la diferencia: un producto usado de vez en cuando no alcanza a mostrar lo que puede hacer en la piel.',
                    'Aplícalo con movimientos suaves, sin frotar ni estirar la piel, especialmente en la zona del contorno de ojos. Cierra bien el envase después de cada uso y guárdalo lejos del calor para conservar la fórmula en buen estado.',
                    'Incorpóralo a tu rutina en el orden que corresponda, de las texturas más ligeras a las más densas. Ante cualquier molestia o reacción, suspende el uso y consulta con un dermatólogo antes de retomar.',
                    'Empléalo sobre el rostro limpio y seco, distribuyendo de manera pareja. En cabina se usa dentro del protocolo de servicio y en casa como parte de la rutina diaria; en ambos casos, la clave es la constancia más que la cantidad.',
                    'Úsalo con la frecuencia que te resulte sostenible y respeta el orden de tu rutina. Guárdalo bien cerrado y evita dejarlo en el baño expuesto a la humedad y al vapor, que es lo que más degrada este tipo de producto.',
                ],
            ],

            // ── Pestañas ───────────────────────────────────────────────────
            'pestanas' => [
                'label' => 'pestañas',
                'item'  => 'insumo para pestañas',
                'openers' => [
                    ':name hace parte de nuestra línea de pestañas, el surtido pensado para lashistas que trabajan extensión y lifting con protocolo serio.',
                    'En extensión de pestañas el detalle lo es todo. :name es parte de nuestra selección de :cat con esa exigencia.',
                    ':name está en la categoría de :cat de Belleza Áurea, con los insumos que se usan en cabina durante todo el servicio.',
                    'Conoce :name, una referencia de :cat para lashistas que atienden con agenda y necesitan reponer inventario seguido.',
                    ':name entra en nuestra línea de :cat, armada escuchando a quienes trabajan pestaña todos los días.',
                    'Sumamos :name al surtido de :cat porque en cabina de pestañas no se puede improvisar con los insumos.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para cabina de extensión.',
                    'Pertenece a :cat, junto al resto de insumos para lashistas.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso profesional.',
                ],
                'audience' => [
                    'Está dirigido a lashistas en ejercicio que trabajan extensión, volumen o lifting.',
                    'Lo usan profesionales de cabina que atienden servicios de dos o más horas.',
                    'Es un insumo de reposición frecuente para quien tiene agenda llena de pestañas.',
                    'Lo piden tanto cabinas establecidas como lashistas que están montando su espacio.',
                    'Funciona para servicio profesional y para estudiantes que están armando su kit de práctica.',
                ],
                'features' => [
                    'Insumo de cabina para servicio de extensión o lifting de pestañas.',
                    'Se complementa con el resto de la línea de pestañas del catálogo.',
                    'De uso por cliente: hace parte del protocolo de higiene de la cabina.',
                    'Conviene mantenerlo organizado en la mesa auxiliar para no interrumpir el servicio.',
                    'Disponible por unidad y también para reposición al por mayor.',
                    'Pensado para consumo frecuente en cabina con agenda activa.',
                ],
                'usage' => [
                    'Ten todo listo en la mesa auxiliar antes de que la clienta se acueste: en pestañas, interrumpir el servicio para buscar un insumo rompe el ritmo y alarga la sesión. Trabaja con buena luz y respeta tu protocolo de higiene de cabina.',
                    'Úsalo dentro de tu protocolo habitual de servicio, con las manos limpias y el área organizada. Prepara la cantidad que vas a necesitar antes de empezar y descarta lo que corresponda al terminar con cada clienta.',
                    'Intégralo al montaje de tu cabina antes de iniciar. Verifica que el párpado esté limpio y libre de residuos de maquillaje; cualquier resto de grasa afecta el resultado y la duración del trabajo sobre la pestaña natural.',
                    'Empléalo en el paso del servicio que corresponda, trabajando siempre pestaña por pestaña y sin afanes. Mantén tu mesa auxiliar ordenada: en sesiones largas, el orden del espacio se traduce en menos fatiga y mejor acabado.',
                    'Prepáralo junto con el resto del material antes de recibir a la clienta. Aplica tu protocolo de desinfección entre servicios y renueva lo desechable en cada sesión, que es lo que mantiene la cabina en condiciones.',
                    'Úsalo con pinza limpia y movimientos precisos, cuidando siempre el contacto con el párpado. Al terminar, recoge y desinfecta la estación antes de la siguiente clienta para no arrastrar residuos de un servicio al otro.',
                ],
            ],

            // ── Peluquería, cepillos y gorros ──────────────────────────────
            'peluqueria' => [
                'label' => 'peluquería',
                'item'  => 'insumo de peluquería',
                'openers' => [
                    ':name hace parte de nuestra línea de peluquería, el surtido pensado para estilistas que trabajan color, tratamiento y peinado todos los días.',
                    'En peluquería el insumo correcto ahorra media hora de servicio. :name es parte de nuestra selección de :cat con esa lógica.',
                    ':name está en la categoría de :cat de Belleza Áurea, con lo que se usa en silla y en lavacabezas durante la jornada.',
                    'Conoce :name, una referencia de :cat para salones con movimiento y estilistas que trabajan a domicilio.',
                    ':name entra en nuestra línea de :cat, armada con lo que de verdad se gasta en un salón de cabello.',
                    'Sumamos :name al surtido de :cat porque el trabajo de cabello pide herramienta y material que aguanten el uso diario.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para trabajo en salón.',
                    'Pertenece a :cat, junto al resto de insumos y herramientas de cabello.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso profesional.',
                ],
                'audience' => [
                    'Está pensado para estilistas en salón y para quienes atienden cabello a domicilio.',
                    'Lo usan profesionales de peluquería en servicios de color, tratamiento o peinado.',
                    'Es apropiado para uso profesional continuo y también para cuidado del cabello en casa.',
                    'Lo piden salones con agenda constante que reponen insumos cada mes.',
                    'Funciona tanto para estilistas con experiencia como para quien está armando su maletín.',
                ],
                'features' => [
                    'Insumo o herramienta de uso directo en servicio de cabello.',
                    'Se complementa con el resto de la línea de peluquería del catálogo.',
                    'Requiere limpieza entre cliente y cliente según el protocolo del salón.',
                    'Pensado para uso frecuente en jornada de salón.',
                    'Disponible por unidad y también para reposición de inventario al por mayor.',
                    'Conviene guardarlo seco y organizado en el carro auxiliar o el maletín.',
                ],
                'usage' => [
                    'Úsalo en el paso del servicio que corresponda, con el cabello en el estado que pide la técnica: seco, húmedo o toalla seca según el caso. Tenlo a mano en el carro auxiliar para no interrumpir el trabajo con la clienta en la silla.',
                    'Trabájalo por secciones, de atrás hacia adelante, como en cualquier servicio de cabello bien organizado. Divide con pinzas y avanza parejo; el orden en la sección es lo que evita zonas desiguales al final del servicio.',
                    'Intégralo a tu protocolo habitual de salón. Al terminar, límpialo y guárdalo seco: la humedad y los residuos de producto son lo que más acorta la vida útil de los insumos de peluquería.',
                    'Empléalo con el cabello previamente desenredado para no maltratar la fibra. Avanza con calma por cada sección y revisa el resultado en el espejo antes de dar por cerrado el paso; corregir después cuesta mucho más tiempo.',
                    'Prepáralo antes de iniciar el servicio, junto con el resto del material que vas a necesitar. En salón con agenda apretada, tener la estación lista de antemano es lo que permite cumplir los tiempos sin sacrificar el resultado.',
                    'Úsalo según la técnica que estés aplicando y respeta los tiempos de cada paso. Límpialo al terminar con cada cliente y revísalo periódicamente; una herramienta de cabello en mal estado se nota de inmediato en el acabado.',
                ],
            ],

            // ── Depilación ─────────────────────────────────────────────────
            'depilacion' => [
                'label' => 'depilación',
                'item'  => 'insumo de depilación',
                'openers' => [
                    ':name hace parte de nuestra línea de depilación, el surtido para cabinas que trabajan cera y necesitan material de consumo permanente.',
                    'En depilación todo se consume rápido y nada puede faltar. :name es parte de nuestra selección de :cat con ese enfoque.',
                    ':name está en la categoría de :cat de Belleza Áurea, con los insumos que se usan sesión tras sesión en cabina.',
                    'Conoce :name, una referencia de :cat para esteticistas que atienden depilación con agenda constante.',
                    ':name entra en nuestra línea de :cat, armada pensando en el consumo real de una cabina activa.',
                    'Sumamos :name al surtido de :cat porque quedarse sin material a mitad de jornada no es una opción.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para cabina de estética.',
                    'Pertenece a :cat, junto al resto de insumos de cera y accesorios.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso profesional.',
                ],
                'audience' => [
                    'Está dirigido a esteticistas y cabinas que prestan servicio de depilación con cera.',
                    'Lo usan profesionales que atienden varias sesiones al día y reponen material con frecuencia.',
                    'Es apropiado para cabina establecida y también para quien atiende a domicilio.',
                    'Lo piden centros de estética que compran por volumen para toda la temporada.',
                    'Funciona para servicio profesional y para estudiantes en práctica de depilación.',
                ],
                'features' => [
                    'Insumo de cabina para servicio de depilación con cera.',
                    'Se complementa con el resto de la línea de depilación del catálogo.',
                    'De un solo uso por cliente cuando el protocolo de higiene así lo exige.',
                    'Pensado para consumo alto: conviene mantener stock permanente.',
                    'Disponible por unidad y también en presentaciones para compra al por mayor.',
                    'Debe guardarse en lugar seco, cerrado y lejos de fuentes de calor.',
                ],
                'usage' => [
                    'Prepara la zona antes de empezar: piel limpia, seca y sin restos de crema. Ten el material organizado al alcance para no interrumpir la sesión. Y respeta siempre el protocolo de un solo uso en lo que corresponda, sin excepciones.',
                    'Úsalo dentro de tu protocolo habitual de cabina, verificando la temperatura y el estado del material antes del contacto con la piel. Trabaja por zonas pequeñas y revisa la reacción de la piel a medida que avanzas en la sesión.',
                    'Alístalo junto con el resto del material antes de que la clienta entre a cabina. Mantén la camilla y la mesa auxiliar ordenadas, y descarta lo desechable al terminar cada servicio según tu protocolo de bioseguridad.',
                    'Empléalo siguiendo la secuencia de tu servicio y cuidando siempre la sensibilidad de la zona. Al finalizar, aplica el paso de calmado que corresponda y recomienda a la clienta los cuidados posteriores habituales.',
                    'Ten siempre stock disponible antes de abrir la agenda del día. En depilación el material se consume mucho más rápido de lo que se calcula, y quedarse corto a mitad de jornada obliga a reprogramar citas.',
                    'Trabájalo con guantes y sobre piel preparada, respetando las zonas donde no corresponde aplicar. Organiza el descarte desde el inicio del servicio para mantener la cabina limpia entre una clienta y la siguiente.',
                ],
            ],

            // ── Seguridad e higiene ────────────────────────────────────────
            'higiene' => [
                'label' => 'seguridad e higiene',
                'item'  => 'insumo de bioseguridad',
                'openers' => [
                    ':name hace parte de nuestra línea de seguridad e higiene, la que sostiene todo lo demás aunque sea la que menos se luce en redes.',
                    'La bioseguridad es lo primero que una clienta nota al entrar. :name es parte de nuestra selección de :cat con esa responsabilidad.',
                    ':name está en la categoría de :cat de Belleza Áurea, con los insumos que todo salón necesita tener siempre disponibles.',
                    'Conoce :name, una referencia de :cat para profesionales que trabajan con protocolo y no improvisan la higiene.',
                    ':name entra en nuestra línea de :cat, la base sobre la que se monta cualquier servicio de belleza responsable.',
                    'Sumamos :name al surtido de :cat porque un salón se gana la confianza en los detalles de higiene, no en el discurso.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat para protocolo de salón.',
                    'Pertenece a :cat, junto al resto de insumos de bioseguridad.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat de uso profesional.',
                ],
                'audience' => [
                    'Está dirigido a cualquier salón, cabina o profesional independiente que trabaje con protocolo de higiene.',
                    'Lo usan manicuristas, estilistas, lashistas y esteticistas por igual.',
                    'Es un insumo de reposición constante para quien atiende público todos los días.',
                    'Lo piden salones que compran por volumen para cubrir varios meses de operación.',
                    'Funciona tanto en salón establecido como para quien presta servicio a domicilio.',
                ],
                'features' => [
                    'Insumo de bioseguridad para el protocolo de higiene del salón.',
                    'Se complementa con el resto de la línea de seguridad e higiene del catálogo.',
                    'De uso por cliente cuando el protocolo lo exige.',
                    'Consumo alto: conviene mantener stock permanente en el salón.',
                    'Disponible por unidad y también en presentaciones para compra al por mayor.',
                    'Debe almacenarse en lugar seco, limpio y protegido del polvo.',
                ],
                'usage' => [
                    'Úsalo como parte fija de tu protocolo, no como algo ocasional. Déjalo siempre a la vista y al alcance en la estación de trabajo: los protocolos de higiene se cumplen cuando el insumo está disponible sin tener que buscarlo.',
                    'Intégralo a la rutina de apertura y cierre del salón. Verifica el stock al inicio de cada semana y repón antes de quedarte corta; en bioseguridad, quedarse sin material significa suspender el servicio o hacerlo mal.',
                    'Empléalo entre un cliente y otro siguiendo tu protocolo escrito. Mantén el área de trabajo despejada y el descarte organizado desde el comienzo de la jornada para que la limpieza entre servicios sea rápida.',
                    'Ténlo preparado antes de iniciar la atención del día. Las clientas notan el detalle de la higiene y eso construye confianza; es de las inversiones más baratas y más rentables que puede hacer un salón.',
                    'Úsalo según el protocolo de cada servicio y descarta lo correspondiente al terminar. Organiza el almacenamiento en un lugar seco y limpio, separado de los productos cosméticos que estén en uso.',
                    'Mantenlo como insumo permanente de la estación de trabajo. Revisa existencias cada semana y haz la reposición junto con el resto del pedido mensual para no interrumpir la operación del salón.',
                ],
            ],

            // ── Maquillaje ─────────────────────────────────────────────────
            'maquillaje' => [
                'label' => 'maquillaje',
                'item'  => 'producto de maquillaje',
                'openers' => [
                    ':name hace parte de nuestra línea de maquillaje, pensada para quienes trabajan rostro en servicio profesional y para uso personal.',
                    'Un buen maquillaje empieza por tener el producto adecuado a la mano. :name es parte de nuestra selección de :cat.',
                    ':name está en la categoría de :cat de Belleza Áurea, con opciones para el día a día y para servicio de cabina.',
                    'Conoce :name, una referencia de :cat para maquilladoras profesionales y para quien se maquilla a diario.',
                    ':name entra en nuestra línea de :cat, elegida pensando en rendimiento real sobre la piel.',
                    'Sumamos :name al surtido de :cat porque el maquillaje también hace parte de la rutina de cuidado personal.',
                ],
                'bridges' => [
                    'Está clasificado en :cat dentro del catálogo de Belleza Áurea.',
                    'Hace parte de nuestra línea de :cat del catálogo.',
                    'Pertenece a :cat, junto al resto de productos de rostro.',
                    'Lo encuentras en la sección de :cat del catálogo.',
                    'Forma parte de nuestra selección de :cat.',
                ],
                'audience' => [
                    'Es apropiado para maquilladoras profesionales y para uso personal diario.',
                    'Lo usan tanto en servicio de cabina como en el neceser de todos los días.',
                    'Funciona para quien se maquilla a diario y para eventos puntuales.',
                    'Lo piden profesionales que arman su set de trabajo y clientas para su rutina.',
                    'Está pensado para uso frecuente, dentro y fuera de la cabina.',
                ],
                'features' => [
                    'Producto de maquillaje para aplicación sobre el rostro.',
                    'Se complementa con el resto de la línea de maquillaje del catálogo.',
                    'Conviene guardarlo cerrado y lejos de la luz directa y el calor.',
                    'Disponible por unidad y también para pedidos al por mayor.',
                    'Formato cómodo para el neceser o el set de trabajo profesional.',
                    'Debe aplicarse con herramienta limpia para cuidar la piel.',
                ],
                'usage' => [
                    'Aplícalo sobre la piel limpia y preparada, con brocha o esponja limpia. Trabaja en capas delgadas y difumina bien los bordes; es más fácil construir cobertura por etapas que intentar corregir un exceso ya aplicado.',
                    'Úsalo después de tu rutina de cuidado facial, cuando la piel ya haya absorbido los productos previos. Difumina con movimientos suaves y revisa el resultado con luz natural antes de dar por terminado el maquillaje.',
                    'Intégralo al paso que corresponda de tu rutina de maquillaje. Mantén las brochas limpias y guarda el producto bien cerrado: la higiene de las herramientas es lo que más influye en cómo reacciona la piel.',
                    'Empléalo en cabina o en casa siguiendo tu secuencia habitual. Prueba el tono con luz de día antes de comprometerte con una aplicación completa y retíralo al final del día con un desmaquillante adecuado.',
                    'Trabájalo con herramienta limpia y sobre piel preparada. En servicio profesional, usa espátula o aplicador individual por clienta para no contaminar el producto entre un maquillaje y el siguiente.',
                    'Aplícalo poco a poco, revisando el avance en el espejo. Guárdalo en lugar fresco y evita dejarlo en el carro o cerca de una ventana, porque el calor altera la textura de este tipo de producto.',
                ],
            ],

            // ── Fallback ───────────────────────────────────────────────────
            'default' => [
                'label' => 'belleza',
                'item'  => 'producto',
                'openers' => [
                    ':name hace parte del catálogo de Belleza Áurea, armado con productos e insumos de belleza elegidos uno por uno.',
                    'Conoce :name, una de las referencias que seleccionamos para el catálogo de Belleza Áurea pensando en uso real.',
                    ':name está disponible en nuestro catálogo de :cat, dentro de la propuesta de cosmética consciente de Belleza Áurea.',
                    'Te presentamos :name, parte de nuestra selección de :cat para profesionales de belleza y uso personal.',
                    ':name entra a nuestro catálogo de :cat porque responde a una necesidad concreta de quienes trabajan en belleza.',
                    'Sumamos :name a la selección de Belleza Áurea, donde buscamos producto útil antes que producto de moda.',
                ],
                'bridges' => [
                    'Está disponible en el catálogo de Belleza Áurea.',
                    'Hace parte de nuestra selección de :cat.',
                    'Lo encuentras clasificado en :cat dentro del catálogo.',
                    'Pertenece a la línea de :cat de Belleza Áurea.',
                    'Forma parte de nuestro surtido de :cat.',
                ],
                'audience' => [
                    'Es apropiado tanto para uso profesional como para uso personal en casa.',
                    'Lo usan profesionales de belleza y también quienes cuidan su imagen por su cuenta.',
                    'Funciona para quien trabaja en el sector y para quien compra para sí misma.',
                    'Lo piden tanto salones como clientas particulares que buscan buen producto.',
                    'Está pensado para uso frecuente, en salón o en casa.',
                ],
                'features' => [
                    'Producto disponible en el catálogo permanente de Belleza Áurea.',
                    'Se puede combinar con otras referencias del catálogo para armar un pedido completo.',
                    'Disponible por unidad y también para pedidos al por mayor.',
                    'Conviene guardarlo en lugar fresco, seco y lejos de la luz directa.',
                    'Pensado para uso profesional y personal.',
                    'Forma parte de nuestra propuesta de cosmética consciente.',
                ],
                'usage' => [
                    'Úsalo siguiendo las recomendaciones propias de su tipo de producto y revisa siempre el empaque antes del primer uso. Guárdalo cerrado, en lugar fresco y seco, para que conserve sus características durante toda su vida útil.',
                    'Incorpóralo a tu rutina o a tu flujo de trabajo según corresponda. Mantén las manos y las herramientas limpias al manipularlo y cierra bien el envase después de cada uso para protegerlo del polvo y la humedad.',
                    'Empléalo en el momento que corresponda dentro de tu servicio o tu rutina personal. Si tienes dudas sobre la forma correcta de usarlo, escríbenos y te orientamos antes de que lo apliques por primera vez.',
                    'Trabájalo con calma la primera vez para conocer su comportamiento. Guárdalo organizado junto al resto de tus insumos y revisa periódicamente su estado, sobre todo si no lo usas todos los días.',
                    'Intégralo a tu set habitual y dale el cuidado básico de almacenamiento: lugar fresco, seco y lejos de fuentes de calor. Ese cuidado simple es lo que mantiene el producto en condiciones durante más tiempo.',
                    'Úsalo según tu necesidad y mantén el orden en tu estación o tocador. Tener cada producto en su lugar hace que la rutina fluya y evita que se pierda material entre un uso y el siguiente.',
                ],
            ],
        ];
    }
}
