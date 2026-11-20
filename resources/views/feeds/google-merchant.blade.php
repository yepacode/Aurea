{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>Belleza Áurea</title>
        <link>{{ url('/') }}</link>
        <description>Catálogo de productos Belleza Áurea — cosmética e insumos de belleza.</description>
        @foreach($products as $p)
        @php
            $firstImage    = is_array($p->images) ? ($p->images[0] ?? null) : null;
            // Imagen en JPG (vía endpoint /img/jpg): Meta rechaza WebP; Google acepta JPG igual.
            $imageAbsolute = $firstImage
                ? jpg_url($firstImage)
                : asset('img/brand/logo-principal.png');
            $desc = trim(strip_tags($p->description ?? $p->meta_description ?? $p->name));
            $desc = $desc !== '' ? $desc : $p->name;
            $availability = ((int) $p->stock) > 0 ? 'in stock' : 'out of stock';
            // Google: <g:price> = precio regular; <g:sale_price> = precio con descuento (opcional).
            // En la app: $p->price = lo que paga el cliente; $p->compare_price = PVP/tachado.
            $onSale = $p->compare_price && (float) $p->compare_price > (float) $p->price;
            $regularPrice = $onSale ? (float) $p->compare_price : (float) $p->price;
            $priceFormatted = number_format($regularPrice, 2, '.', '') . ' COP';
            $salePriceFormatted = $onSale
                ? number_format((float) $p->price, 2, '.', '') . ' COP'
                : null;
            $brandName = $p->brand?->name ?? 'Belleza Áurea';
            $hasGtin = ! empty($p->gtin);
            $hasMpn  = ! empty($p->mpn);

            // Categoría oficial de Google por tipo de producto (mejor emparejamiento que la genérica "Cosmetics").
            $NAIL   = 'Health & Beauty > Personal Care > Cosmetics > Nail Care';
            $POLISH = $NAIL.' > Nail Polish';
            $TOOLS  = $NAIL.' > Manicure & Pedicure Tools';
            $HAIR   = 'Health & Beauty > Personal Care > Hair Care';
            $SKIN   = 'Health & Beauty > Personal Care > Cosmetics > Skin Care';
            $gcMap = [
                'unas modernas' => $POLISH, 'esmaltes' => $POLISH, 'preparadores y finalizadores' => $POLISH,
                'unas' => $NAIL, 'decoracion' => $NAIL, 'construccion y esculpido' => $NAIL,
                'bledos' => $NAIL, 'remocion y mantenimiento' => $NAIL,
                'herramientas manuales' => $TOOLS, 'pinceleria' => $TOOLS, 'equipos electricos' => $TOOLS,
                'brocas' => $TOOLS, 'tijeras' => $TOOLS, 'limas' => $TOOLS,
                'pestanas' => 'Health & Beauty > Personal Care > Cosmetics > Makeup > Eye Makeup > False Eyelashes',
                'peluqueria' => $HAIR, 'cepillos' => $HAIR, 'gorros' => $HAIR,
                'rostro y piel' => $SKIN,
                'depilacion' => 'Health & Beauty > Personal Care > Hair Removal',
                'seguridad e higiene' => 'Health & Beauty > Personal Care',
            ];
            $catKey = (string) \Illuminate\Support\Str::of($p->category?->name ?? '')->lower()->ascii();
            $googleCategory = $gcMap[$catKey] ?? 'Health & Beauty > Personal Care > Cosmetics';
        @endphp
        <item>
            <g:id>{{ $p->id }}</g:id>
            <g:title><![CDATA[{{ \Illuminate\Support\Str::limit($p->name, 150, '') }}]]></g:title>
            <g:description><![CDATA[{{ \Illuminate\Support\Str::limit($desc, 5000, '') }}]]></g:description>
            <g:link>{{ route('products.show', $p->slug) }}</g:link>
            <g:image_link>{{ $imageAbsolute }}</g:image_link>
            <g:availability>{{ $availability }}</g:availability>
            <g:price>{{ $priceFormatted }}</g:price>
            @if($salePriceFormatted)
            <g:sale_price>{{ $salePriceFormatted }}</g:sale_price>
            @endif
            <g:brand><![CDATA[{{ $brandName }}]]></g:brand>
            <g:condition>new</g:condition>
            <g:google_product_category>{{ $googleCategory }}</g:google_product_category>
            @if($p->category)
            <g:product_type><![CDATA[{{ $p->category->name }}]]></g:product_type>
            @endif
            @if($hasGtin)
            <g:gtin>{{ $p->gtin }}</g:gtin>
            @endif
            @if($hasMpn)
            <g:mpn>{{ $p->mpn }}</g:mpn>
            @endif
            <g:identifier_exists>{{ ($hasGtin || $hasMpn) ? 'yes' : 'no' }}</g:identifier_exists>
        </item>
        @endforeach
    </channel>
</rss>
