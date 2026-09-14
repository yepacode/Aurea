@extends('layouts.admin')

@section('title', 'Notificaciones push')
@section('page_title', 'Notificaciones push')

@section('content')
<div class="max-w-4xl">
    <p class="text-sm text-gray-600 mb-6">
        Envía una notificación push a <strong>todos</strong> los navegadores suscritos.
        Ideal para promociones flash, nuevos lanzamientos o campañas especiales.
    </p>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Suscritos totales</p>
            <p class="text-3xl font-semibold text-gray-900">{{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Últimos 30 días</p>
            <p class="text-3xl font-semibold text-gray-900">{{ number_format($last30d, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">Identificados / anónimos</p>
            <p class="text-3xl font-semibold text-gray-900">
                {{ number_format($identified, 0, ',', '.') }}
                <span class="text-gray-400 text-lg">/ {{ number_format($anon, 0, ',', '.') }}</span>
            </p>
        </div>
    </div>

    {{-- Broadcast form --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Enviar broadcast</h2>

        <form method="POST" action="{{ route('admin.push.send') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" name="title" required maxlength="80"
                       placeholder="Ej: ✨ Nueva colección disponible"
                       value="{{ old('title') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                <p class="text-xs text-gray-500 mt-1">Máx. 80 caracteres. Aparece como título de la notificación.</p>
                @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje <span class="text-red-500">*</span></label>
                <textarea name="body" required maxlength="200" rows="3"
                          placeholder="Ej: Descubre nuestros lanzamientos con 15% de descuento por lanzamiento."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">{{ old('body') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Máx. 200 caracteres.</p>
                @error('body')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen (URL absoluta, opcional)</label>
                <input type="url" name="image" maxlength="500"
                       placeholder="https://bellezaaurea.com/img/promo/oferta.jpg"
                       value="{{ old('image') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                @error('image')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL al hacer clic (opcional)</label>
                <input type="url" name="url" maxlength="500"
                       placeholder="{{ url('/productos') }}"
                       value="{{ old('url', url('/productos')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                <p class="text-xs text-gray-500 mt-1">A dónde va el cliente al hacer clic. Si se deja vacío, va a la portada.</p>
                @error('url')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2 flex items-center gap-3">
                <button type="submit"
                        onclick="return confirm('Se enviará a {{ $total }} navegadores suscritos. ¿Continuar?');"
                        class="px-5 py-2.5 rounded-lg text-white font-medium"
                        style="background:#BE9A53;">
                    Enviar a {{ number_format($total, 0, ',', '.') }} suscritos
                </button>
                <span class="text-xs text-gray-500">Se envía inmediatamente. No se puede cancelar.</span>
            </div>
        </form>
    </div>

    @if(!config('services.webpush.vapid.public_key') || !config('services.webpush.vapid.private_key'))
    <div class="mt-6 bg-yellow-50 border border-yellow-300 text-yellow-900 rounded-lg p-4 text-sm">
        <strong>Falta configurar VAPID.</strong> Corre en el servidor:
        <code class="block mt-2 bg-yellow-100 rounded px-2 py-1">php artisan push:generate-keys</code>
        y luego <code>php artisan config:clear</code>. Sin esto no se pueden enviar notificaciones.
    </div>
    @endif
</div>
@endsection
