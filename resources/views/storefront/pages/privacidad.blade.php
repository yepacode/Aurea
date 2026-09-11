@extends('layouts.app')

@section('title', 'Política de privacidad y tratamiento de datos | ' . config('legal.brand_name'))
@section('meta_description', 'Política de tratamiento de datos personales de ' . config('legal.brand_name') . ' conforme a la Ley 1581 de 2012 (Habeas Data) y el Decreto 1377 de 2013.')
@section('canonical', route('legal.privacy'))

@php
    $ph = fn ($v) => \Illuminate\Support\Str::startsWith((string) $v, '[')
        ? '<span class="placeholder">' . e($v) . '</span>'
        : e($v);
    $brand   = config('legal.brand_name');
    $company = config('legal.company_name');
    $nit     = config('legal.nit');
    $email   = config('legal.email');
    $phone   = config('legal.phone');
    $address = config('legal.address');
    $city    = config('legal.city');
    $country = config('legal.country');
@endphp

@section('content')
    @include('partials.legal-hero', [
        'title'    => 'Política de privacidad y tratamiento de datos',
        'subtitle' => 'Cómo recolectamos, usamos y protegemos tus datos personales. Tu Habeas Data, garantizado.',
        'current'  => 'Política de privacidad',
    ])

    @include('partials.legal-styles')

    <div class="legal-wrap">
        <p class="legal-meta">Última actualización: {{ config('legal.updated_at') }}</p>

        <div class="legal-toc">
            <h4>Contenido</h4>
            <ol>
                <li><a href="#p1">Responsable del tratamiento</a></li>
                <li><a href="#p2">Marco legal</a></li>
                <li><a href="#p3">Definiciones</a></li>
                <li><a href="#p4">Datos que recolectamos</a></li>
                <li><a href="#p5">Finalidades del tratamiento</a></li>
                <li><a href="#p6">Autorización del titular</a></li>
                <li><a href="#p7">Derechos del titular (Habeas Data)</a></li>
                <li><a href="#p8">Ejercicio de derechos: consultas y reclamos</a></li>
                <li><a href="#p9">Transferencia y transmisión de datos</a></li>
                <li><a href="#p10">Seguridad de la información</a></li>
                <li><a href="#p11">Datos de menores de edad</a></li>
                <li><a href="#p12">Cookies</a></li>
                <li><a href="#p13">Vigencia</a></li>
            </ol>
        </div>

        <div class="legal-prose">
            <h2 id="p1"><span class="num">1.</span>Responsable del tratamiento</h2>
            @php
                $p1 = 'El responsable del tratamiento de los datos personales recolectados a través de este sitio es '.e($company ?: $brand).' («'.e($brand).'»)';
                if ($nit) { $p1 .= ', identificada con NIT '.e($nit); }
                $loc = '';
                if ($address && $city && $city !== $address) { $loc = e($address).', '.e($city); }
                elseif ($address) { $loc = e($address); }
                elseif ($city) { $loc = e($city); }
                if ($loc !== '') { $p1 .= ', con domicilio en '.$loc.', '.e($country); }
                $p1 .= '.';
            @endphp
            <p>{!! $p1 !!}</p>
            <ul>
                <li>Correo de contacto: {!! $ph($email) !!}</li>
                <li>Teléfono: {!! $ph($phone) !!}</li>
            </ul>

            <h2 id="p2"><span class="num">2.</span>Marco legal</h2>
            <p>Esta política se elabora en cumplimiento del artículo 15 de la Constitución Política, la Ley 1581 de 2012, el Decreto 1377 de 2013 y demás normas concordantes que desarrollan el derecho fundamental de <strong>Habeas Data</strong> y el tratamiento de datos personales en Colombia.</p>

            <h2 id="p3"><span class="num">3.</span>Definiciones</h2>
            <ul>
                <li><strong>Dato personal:</strong> cualquier información vinculada a una persona natural determinada o determinable.</li>
                <li><strong>Titular:</strong> persona natural cuyos datos son objeto de tratamiento.</li>
                <li><strong>Tratamiento:</strong> cualquier operación sobre datos personales (recolección, almacenamiento, uso, circulación o supresión).</li>
                <li><strong>Responsable:</strong> quien decide sobre el tratamiento de los datos ({{ $brand }}).</li>
                <li><strong>Encargado:</strong> quien realiza el tratamiento por cuenta del responsable (por ejemplo, proveedores de hosting o pasarelas de pago).</li>
                <li><strong>Autorización:</strong> consentimiento previo, expreso e informado del titular.</li>
            </ul>

            <h2 id="p4"><span class="num">4.</span>Datos que recolectamos</h2>
            <ul>
                <li><strong>Identificación y contacto:</strong> nombre, documento, correo, teléfono, dirección de envío y facturación.</li>
                <li><strong>Datos de la compra:</strong> productos adquiridos, historial de pedidos y preferencias.</li>
                <li><strong>Datos de pago:</strong> gestionados directamente por la pasarela de pago; {{ $brand }} no almacena los números completos de tarjetas.</li>
                <li><strong>Datos de navegación:</strong> dirección IP, tipo de dispositivo y navegador, páginas visitadas y cookies (ver <a href="{{ route('legal.cookies') }}">Política de Cookies</a>).</li>
            </ul>

            <h2 id="p5"><span class="num">5.</span>Finalidades del tratamiento</h2>
            <ul>
                <li>Gestionar el registro, los pedidos, los pagos y las entregas.</li>
                <li>Prestar servicio al cliente, atender PQR y hacer efectivas garantías.</li>
                <li>Enviar comunicaciones comerciales, promociones y novedades, cuando el titular lo haya autorizado.</li>
                <li>Realizar análisis estadísticos y mejorar la experiencia del sitio.</li>
                <li>Cumplir obligaciones legales, contables y tributarias.</li>
            </ul>

            <h2 id="p6"><span class="num">6.</span>Autorización del titular</h2>
            <p>Al registrarse, comprar o suscribirse al boletín, el titular autoriza de manera previa, expresa e informada el tratamiento de sus datos para las finalidades aquí descritas. La autorización podrá otorgarse por medios electrónicos y se conservará como prueba del consentimiento.</p>

            <h2 id="p7"><span class="num">7.</span>Derechos del titular (Habeas Data)</h2>
            <p>Como titular de los datos, usted tiene derecho a:</p>
            <ul>
                <li><strong>Conocer, actualizar y rectificar</strong> sus datos personales.</li>
                <li><strong>Solicitar prueba</strong> de la autorización otorgada.</li>
                <li>Ser <strong>informado</strong> sobre el uso que se ha dado a sus datos.</li>
                <li>Presentar <strong>quejas</strong> ante la Superintendencia de Industria y Comercio (SIC) por infracciones a la ley.</li>
                <li><strong>Revocar la autorización</strong> y/o <strong>solicitar la supresión</strong> de sus datos, salvo deber legal o contractual de conservarlos.</li>
                <li><strong>Acceder gratuitamente</strong> a sus datos personales objeto de tratamiento.</li>
            </ul>

            <h2 id="p8"><span class="num">8.</span>Ejercicio de derechos: consultas y reclamos</h2>
            <p>El titular o sus causahabientes podrán ejercer sus derechos enviando una solicitud a {!! $ph($email) !!}, indicando su nombre, documento de identidad, el objeto de la petición y datos de contacto.</p>
            <ul>
                <li><strong>Consultas:</strong> se atenderán en un término máximo de <strong>diez (10) días hábiles</strong>. Si no es posible, se informará al interesado y el plazo podrá ampliarse hasta cinco (5) días hábiles más.</li>
                <li><strong>Reclamos:</strong> se resolverán en un término máximo de <strong>quince (15) días hábiles</strong> contados a partir del día siguiente a su recibo. De no ser posible, se informarán los motivos y la fecha en que se atenderá, sin superar ocho (8) días hábiles adicionales.</li>
            </ul>

            <h2 id="p9"><span class="num">9.</span>Transferencia y transmisión de datos</h2>
            <p>{{ $brand }} podrá compartir datos con encargados que apoyan su operación (hosting, pasarelas de pago, transportadoras, herramientas de correo y analítica), quienes tratan los datos únicamente conforme a las instrucciones del responsable y con las debidas garantías de confidencialidad y seguridad.</p>

            <h2 id="p10"><span class="num">10.</span>Seguridad de la información</h2>
            <p>Adoptamos medidas técnicas, humanas y administrativas razonables para proteger los datos y evitar su adulteración, pérdida, consulta, uso o acceso no autorizado. La transmisión de información se realiza mediante conexiones cifradas (HTTPS).</p>

            <h2 id="p11"><span class="num">11.</span>Datos de menores de edad</h2>
            <p>El tratamiento de datos de menores solo procederá cuando responda a su interés superior y con autorización del representante legal. El sitio está dirigido a mayores de edad.</p>

            <h2 id="p12"><span class="num">12.</span>Cookies</h2>
            <p>Este sitio utiliza cookies y tecnologías similares para su funcionamiento y análisis. Consulta el detalle y cómo gestionarlas en nuestra <a href="{{ route('legal.cookies') }}">Política de Cookies</a>.</p>

            <h2 id="p13"><span class="num">13.</span>Vigencia</h2>
            <p>Esta política rige a partir de su publicación y podrá ser modificada. Las bases de datos se conservarán mientras sea necesario para cumplir las finalidades y las obligaciones legales aplicables. Cualquier cambio sustancial será informado a través del sitio.</p>

            <div class="legal-contact">
                <h3>Ejerce tus derechos de Habeas Data</h3>
                @php
                    $foot1 = 'Responsable: '.e($company ?: $brand).($nit ? ' — NIT '.e($nit) : '');
                    $foot2 = 'Correo: '.e($email).($phone ? ' · Tel: '.e($phone) : '');
                    if ($address && $city && $city !== $address) { $foot3 = e($address).', '.e($city).', '.e($country); }
                    elseif ($address) { $foot3 = e($address).', '.e($country); }
                    elseif ($city) { $foot3 = e($city).', '.e($country); }
                    else { $foot3 = e($country); }
                @endphp
                <p>{!! $foot1 !!}</p>
                <p>{!! $foot2 !!}</p>
                <p>{!! $foot3 !!}</p>
            </div>
        </div>
    </div>
@endsection
