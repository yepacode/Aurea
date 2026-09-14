@extends('layouts.admin')

@section('title', 'Analítica (GA4 y Meta Pixel)')
@section('page_title', 'Analítica (Google Analytics 4 y Meta Pixel)')

@section('content')
<div class="max-w-2xl mx-auto">

    <p class="text-sm text-gray-500 mb-6">
        Pega aquí los identificadores de <strong>Google Analytics 4</strong> y <strong>Meta Pixel</strong>.
        En cuanto se guarden, la tienda empezará a enviar eventos (vistas, agregado al carrito,
        checkout, compras). En entorno de desarrollo local <em>no</em> se envía nada para no
        ensuciar tus métricas.
    </p>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-5 flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.analytics.update') }}">
        @method('PUT')
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- GA4 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics 4 — Measurement ID</label>
                <input type="text" name="ga4_measurement_id"
                       value="{{ $ga4_measurement_id }}"
                       placeholder="G-XXXXXXXXXX"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                <p class="text-xs text-gray-500 mt-1">
                    Lo encuentras en
                    <a href="https://analytics.google.com/" target="_blank" rel="noopener" class="text-blue-600 hover:underline">analytics.google.com</a>
                    &rarr; Admin &rarr; Streams de datos &rarr; Web &rarr; <strong>Measurement ID</strong>.
                    Empieza con <code class="bg-gray-100 px-1 rounded">G-</code>.
                </p>
            </div>

            {{-- Meta Pixel --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Pixel — ID</label>
                <input type="text" name="meta_pixel_id"
                       value="{{ $meta_pixel_id }}"
                       placeholder="123456789012345"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                <p class="text-xs text-gray-500 mt-1">
                    Lo encuentras en
                    <a href="https://business.facebook.com/events_manager" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Events Manager</a>
                    &rarr; Data Sources &rarr; tu Pixel &rarr; <strong>Pixel ID</strong>. Suele tener 15 o 16 dígitos.
                </p>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-800 px-3 py-2 text-xs">
                <p><strong>¿Qué se envía?</strong> Vistas de página, ver producto, agregar al carrito, iniciar checkout, agregar pago y compras. Los eventos siguen los nombres estándar de Google y Meta (view_item / ViewContent, add_to_cart / AddToCart, purchase / Purchase, …).</p>
            </div>

            <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 px-3 py-2 text-xs">
                <p><strong>Entorno actual:</strong>
                    <code class="bg-white px-1 rounded border">{{ app()->environment() }}</code>.
                    @if(app()->environment('local'))
                        Estás en <strong>local</strong>: los scripts NO se cargan aunque pegues los IDs.
                    @else
                        Los eventos se cargarán en la tienda de inmediato.
                    @endif
                </p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </div>
    </form>

    <p class="text-xs text-gray-400 mt-4">
        Alternativa avanzada: si el equipo técnico prefiere manejarlo por servidor, también se aceptan
        las variables <code>GA4_MEASUREMENT_ID</code> y <code>META_PIXEL_ID</code> del archivo
        <code>.env</code>; lo guardado desde este panel tiene prioridad.
    </p>
</div>
@endsection
