@extends('layouts.app')

@section('title', 'Política de cookies | ' . config('legal.brand_name'))
@section('meta_description', 'Política de cookies de ' . config('legal.brand_name') . '. Qué cookies usamos, para qué sirven y cómo puedes gestionarlas o desactivarlas.')
@section('canonical', route('legal.cookies'))

@php
    $ph = fn ($v) => \Illuminate\Support\Str::startsWith((string) $v, '[')
        ? '<span class="placeholder">' . e($v) . '</span>'
        : e($v);
    $brand = config('legal.brand_name');
    $email = config('legal.email');
@endphp

@section('content')
    @include('partials.legal-hero', [
        'title'    => 'Política de cookies',
        'subtitle' => 'Qué son las cookies, para qué las usamos y cómo puedes controlarlas.',
        'current'  => 'Política de cookies',
    ])

    @include('partials.legal-styles')

    <div class="legal-wrap">
        <p class="legal-meta">Última actualización: {{ config('legal.updated_at') }}</p>

        <div class="legal-prose">
            <h2 id="c1"><span class="num">1.</span>¿Qué son las cookies?</h2>
            <p>Las cookies son pequeños archivos de texto que un sitio web almacena en tu dispositivo cuando lo visitas. Permiten que el sitio recuerde tus acciones y preferencias (como el contenido del carrito o el idioma) durante un tiempo, para que no tengas que volver a configurarlas cada vez.</p>

            <h2 id="c2"><span class="num">2.</span>Tipos de cookies que utilizamos</h2>
            <ul>
                <li><strong>Técnicas o necesarias:</strong> imprescindibles para el funcionamiento del sitio (sesión, carrito de compras, seguridad). No requieren consentimiento.</li>
                <li><strong>De preferencias:</strong> recuerdan tus elecciones para personalizar tu experiencia.</li>
                <li><strong>Analíticas:</strong> nos ayudan a entender cómo se usa el sitio para mejorarlo (de forma agregada y anónima).</li>
                <li><strong>De marketing/publicidad:</strong> permiten mostrar contenido y anuncios relevantes; solo se activan con tu consentimiento.</li>
            </ul>

            <h2 id="c3"><span class="num">3.</span>Cookies utilizadas en este sitio</h2>
            <p>A modo de referencia, estas son las categorías de cookies que puede utilizar {{ $brand }}. Completa o ajusta esta tabla según las herramientas reales instaladas.</p>
            <div class="legal-table-wrap">
                <table class="legal-table">
                    <thead>
                        <tr><th>Cookie</th><th>Tipo</th><th>Finalidad</th><th>Duración</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>laravel_session</td><td>Técnica</td><td>Mantener la sesión del usuario.</td><td>Sesión</td></tr>
                        <tr><td>XSRF-TOKEN</td><td>Técnica</td><td>Seguridad frente a ataques CSRF.</td><td>Sesión</td></tr>
                        <tr><td>carrito</td><td>Técnica</td><td>Recordar los productos del carrito.</td><td>Hasta 30 días</td></tr>
                        <tr><td><span class="placeholder">_ga / _gid</span></td><td>Analítica</td><td>Medición de audiencia (Google Analytics).</td><td>Hasta 2 años</td></tr>
                        <tr><td><span class="placeholder">_fbp</span></td><td>Marketing</td><td>Publicidad y remarketing (Meta Pixel).</td><td>Hasta 3 meses</td></tr>
                    </tbody>
                </table>
            </div>

            <h2 id="c4"><span class="num">4.</span>Cómo gestionar o desactivar las cookies</h2>
            <p>Puedes permitir, bloquear o eliminar las cookies desde la configuración de tu navegador. Ten en cuenta que desactivar las cookies técnicas puede afectar el funcionamiento del sitio (por ejemplo, el carrito de compras).</p>
            <ul>
                <li><strong>Chrome:</strong> Configuración → Privacidad y seguridad → Cookies y otros datos de sitios.</li>
                <li><strong>Firefox:</strong> Ajustes → Privacidad &amp; Seguridad → Cookies y datos del sitio.</li>
                <li><strong>Safari:</strong> Preferencias → Privacidad → Gestionar datos de sitios web.</li>
                <li><strong>Edge:</strong> Configuración → Cookies y permisos del sitio.</li>
            </ul>

            <h2 id="c5"><span class="num">5.</span>Consentimiento</h2>
            <p>Al continuar navegando en el sitio, aceptas el uso de las cookies necesarias. Las cookies analíticas y de marketing solo se activan con tu consentimiento, que podrás retirar en cualquier momento ajustando la configuración de tu navegador.</p>

            <h2 id="c6"><span class="num">6.</span>Cambios en esta política</h2>
            <p>Podremos actualizar esta Política de Cookies para reflejar cambios en las tecnologías utilizadas o en la normativa aplicable. La versión vigente será siempre la publicada en este sitio.</p>

            <h2 id="c7"><span class="num">7.</span>Más información</h2>
            <p>Para conocer cómo tratamos tus datos personales, consulta nuestra <a href="{{ route('legal.privacy') }}">Política de Privacidad y Tratamiento de Datos</a>. Si tienes dudas, escríbenos a {!! $ph($email) !!}.</p>

            <div class="legal-contact">
                <h3>¿Preguntas sobre cookies?</h3>
                <p>Estamos para ayudarte: {!! $ph($email) !!}</p>
            </div>
        </div>
    </div>
@endsection
