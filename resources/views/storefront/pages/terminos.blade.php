@extends('layouts.app')

@section('title', 'Términos y condiciones | ' . config('legal.brand_name'))
@section('meta_description', 'Términos y condiciones de uso y compra en ' . config('legal.brand_name') . '. Condiciones de venta, pagos, envíos, derecho de retracto y garantías.')
@section('canonical', route('legal.terms'))

@php
    // Muestra los valores del config; si es un placeholder [ ... ] lo resalta.
    $ph = fn ($v) => \Illuminate\Support\Str::startsWith((string) $v, '[')
        ? '<span class="placeholder">' . e($v) . '</span>'
        : e($v);
    $brand   = config('legal.brand_name');
    $company = config('legal.company_name');
    $nit     = config('legal.nit');
    $email   = config('legal.email');
    $city    = config('legal.city');
    $country = config('legal.country');
    $retracto = config('legal.retracto_dias', 5);
@endphp

@section('content')
    @include('partials.legal-hero', [
        'title'    => 'Términos y condiciones',
        'subtitle' => 'Las reglas que rigen el uso de nuestra tienda y la compra de nuestros productos.',
        'current'  => 'Términos y condiciones',
    ])

    @include('partials.legal-styles')

    <div class="legal-wrap">
        <p class="legal-meta">Última actualización: {{ config('legal.updated_at') }}</p>

        <div class="legal-note">
            <strong>Documento de plantilla.</strong> Reemplaza los campos resaltados (entre corchetes) por los datos reales de la empresa y haz revisar este texto por tu abogado antes de publicarlo. Este contenido es una guía general y no constituye asesoría jurídica.
        </div>

        <div class="legal-toc">
            <h4>Contenido</h4>
            <ol>
                <li><a href="#t1">Identificación y aceptación</a></li>
                <li><a href="#t2">Objeto</a></li>
                <li><a href="#t3">Cuenta de usuario</a></li>
                <li><a href="#t4">Productos, precios y disponibilidad</a></li>
                <li><a href="#t5">Proceso de compra</a></li>
                <li><a href="#t6">Formas de pago</a></li>
                <li><a href="#t7">Envíos y entregas</a></li>
                <li><a href="#t8">Derecho de retracto</a></li>
                <li><a href="#t9">Reversión del pago</a></li>
                <li><a href="#t10">Garantía legal</a></li>
                <li><a href="#t11">Propiedad intelectual</a></li>
                <li><a href="#t12">Tratamiento de datos personales</a></li>
                <li><a href="#t13">Responsabilidad</a></li>
                <li><a href="#t14">Modificaciones</a></li>
                <li><a href="#t15">Ley aplicable y jurisdicción</a></li>
            </ol>
        </div>

        <div class="legal-prose">
            <h2 id="t1"><span class="num">1.</span>Identificación y aceptación</h2>
            <p>El presente sitio web y tienda en línea <strong>{{ $brand }}</strong> es operado por {!! $ph($company) !!}, identificada con NIT {!! $ph($nit) !!}, con domicilio en {!! $ph($city) !!}, {{ $country }} (en adelante, «{{ $brand }}», «nosotros» o «el titular»).</p>
            <p>Al navegar, registrarse o realizar una compra en este sitio, el usuario declara que ha leído, entendido y aceptado íntegramente estos Términos y Condiciones, así como la <a href="{{ route('legal.privacy') }}">Política de Privacidad</a> y la <a href="{{ route('legal.cookies') }}">Política de Cookies</a>. Si no está de acuerdo, debe abstenerse de utilizar el sitio.</p>

            <h2 id="t2"><span class="num">2.</span>Objeto</h2>
            <p>Estos términos regulan el acceso y uso del sitio, así como las condiciones de compra de los productos de belleza, cosmética y cuidado personal ofrecidos por {{ $brand }}. La relación se rige por la legislación colombiana, en especial la Ley 1480 de 2011 (Estatuto del Consumidor) y demás normas aplicables al comercio electrónico.</p>

            <h2 id="t3"><span class="num">3.</span>Cuenta de usuario</h2>
            <p>Para ciertas funciones puede ser necesario registrarse. El usuario se compromete a suministrar información veraz, completa y actualizada, y es responsable de la confidencialidad de sus credenciales y de toda actividad realizada desde su cuenta. Debe ser mayor de edad o contar con autorización de su representante legal para efectuar compras.</p>

            <h2 id="t4"><span class="num">4.</span>Productos, precios y disponibilidad</h2>
            <ul>
                <li>Los productos se describen y muestran con la mayor exactitud posible; sin embargo, los colores pueden variar según la pantalla del dispositivo.</li>
                <li>Los precios se expresan en pesos colombianos (COP) e incluyen los impuestos aplicables, salvo que se indique lo contrario.</li>
                <li>{{ $brand }} podrá modificar precios y catálogo en cualquier momento. El precio aplicable será el vigente al momento de confirmar el pedido.</li>
                <li>La disponibilidad está sujeta a existencias. Si un producto no está disponible tras la compra, se informará al cliente y se procederá al reembolso correspondiente.</li>
            </ul>

            <h2 id="t5"><span class="num">5.</span>Proceso de compra</h2>
            <p>El cliente selecciona los productos, los agrega al carrito, diligencia los datos de envío y facturación, elige el método de pago y confirma el pedido. Una vez confirmado y aprobado el pago, se enviará una confirmación al correo registrado. Dicha confirmación constituye la prueba de la transacción.</p>

            <h2 id="t6"><span class="num">6.</span>Formas de pago</h2>
            <p>Los pagos se procesan a través de pasarelas de pago seguras (por ejemplo, tarjetas de crédito/débito y demás medios habilitados). {{ $brand }} no almacena los datos completos de las tarjetas; su tratamiento corresponde a la pasarela de pago bajo sus propios estándares de seguridad (PCI-DSS). El pedido se despacha una vez el pago sea aprobado.</p>

            <h2 id="t7"><span class="num">7.</span>Envíos y entregas</h2>
            <p>Las condiciones, costos y tiempos de envío se detallan en la página de <a href="{{ route('shipping-returns') }}">Envíos y devoluciones</a>. Los tiempos son estimados y pueden variar por causas ajenas a {{ $brand }} (transportadora, fuerza mayor, zonas de difícil acceso).</p>

            <h2 id="t8"><span class="num">8.</span>Derecho de retracto</h2>
            <p>Conforme al artículo 47 de la Ley 1480 de 2011, el consumidor podrá ejercer el <strong>derecho de retracto</strong> dentro de los <strong>{{ $retracto }} días hábiles</strong> siguientes a la entrega del producto, devolviéndolo en las mismas condiciones en que lo recibió. {{ $brand }} reintegrará el dinero pagado. Los costos de transporte y demás gastos de la devolución serán asumidos por el consumidor. Este derecho no aplica a los bienes exceptuados por la ley (por ejemplo, productos de uso personal por razones de higiene una vez abiertos).</p>

            <h2 id="t9"><span class="num">9.</span>Reversión del pago</h2>
            <p>En los casos previstos en el artículo 51 de la Ley 1480 de 2011 (fraude, operación no solicitada, producto no recibido, producto defectuoso o distinto al solicitado), el consumidor podrá solicitar la reversión del pago dentro de los términos legales, notificando a {{ $brand }} y a la entidad emisora del medio de pago.</p>

            <h2 id="t10"><span class="num">10.</span>Garantía legal</h2>
            <p>Todos los productos cuentan con la garantía legal establecida en el Estatuto del Consumidor por defectos de calidad o idoneidad. Para hacerla efectiva, el cliente puede comunicarse a {!! $ph($email) !!} adjuntando el número de pedido y la evidencia correspondiente.</p>

            <h2 id="t11"><span class="num">11.</span>Propiedad intelectual</h2>
            <p>Todos los contenidos del sitio (marca, logotipos, textos, imágenes, diseños, videos y código) son propiedad de {{ $brand }} o de sus licenciantes y están protegidos por las normas de propiedad intelectual. Queda prohibida su reproducción, distribución o uso no autorizado.</p>

            <h2 id="t12"><span class="num">12.</span>Tratamiento de datos personales</h2>
            <p>El tratamiento de los datos personales del usuario se rige por nuestra <a href="{{ route('legal.privacy') }}">Política de Privacidad y Tratamiento de Datos</a>, elaborada conforme a la Ley 1581 de 2012 y el Decreto 1377 de 2013.</p>

            <h2 id="t13"><span class="num">13.</span>Responsabilidad</h2>
            <p>{{ $brand }} no será responsable por interrupciones del servicio, fallas técnicas ajenas a su control, ni por el uso indebido de los productos por parte del usuario. El sitio se ofrece «tal cual» y {{ $brand }} realiza sus mejores esfuerzos para mantener la información actualizada y el servicio disponible.</p>

            <h2 id="t14"><span class="num">14.</span>Modificaciones</h2>
            <p>{{ $brand }} podrá modificar estos Términos y Condiciones en cualquier momento. Los cambios regirán desde su publicación en el sitio. Se recomienda revisarlos periódicamente.</p>

            <h2 id="t15"><span class="num">15.</span>Ley aplicable y jurisdicción</h2>
            <p>Estos términos se rigen por las leyes de la República de {{ $country }}. Cualquier controversia se someterá a los jueces y tribunales competentes de {!! $ph($city) !!}, sin perjuicio de los mecanismos de protección al consumidor ante la Superintendencia de Industria y Comercio (SIC).</p>

            <div class="legal-contact">
                <h3>¿Dudas sobre estos términos?</h3>
                <p>Escríbenos y con gusto te ayudamos.</p>
                <p>Correo: {!! $ph($email) !!}</p>
                <p>{{ $company !== '[RAZÓN SOCIAL S.A.S.]' ? $company : '' }} {!! $ph($city) !!}, {{ $country }}</p>
            </div>
        </div>
    </div>
@endsection
