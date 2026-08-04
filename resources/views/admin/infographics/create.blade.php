@extends('layouts.admin')

@section('title', 'Nueva infografía')
@section('page_title', 'Nueva infografía')

@section('content')
    <form method="POST" action="{{ route('admin.infographics.store') }}" enctype="multipart/form-data" class="max-w-3xl space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información</h2>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           placeholder="Ej: Rutina de skincare paso a paso"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                    <textarea id="description" name="description" rows="2"
                              placeholder="Breve descripción de la infografía"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Activa</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Imagen *</h2>

            <div class="mb-4 p-4 rounded-lg border border-blue-200 bg-blue-50">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Tamaño recomendado</p>
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>1280 × 720 px</strong> (proporción 16:9, horizontal).
                            Para pantallas retina usa <strong>1920 × 1080 px</strong>.
                        </p>
                        <p class="text-xs text-blue-600 mt-1">La infografía se muestra en un contenedor 16:9. Imágenes verticales o cuadradas se recortarán automáticamente y no se verán bien.</p>
                    </div>
                </div>
            </div>

            <input type="file" name="image" accept="image/*" required
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="mt-2 text-xs text-gray-400">JPG, PNG o WebP. Máximo 4MB.</p>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.infographics.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Crear infografía
            </button>
        </div>
    </form>
@endsection
