@extends('layouts.admin')

@section('title', 'Editar página del blog')
@section('page_title', 'Página del blog')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <p class="text-sm text-gray-500 mb-6">Edita los textos del hero que se muestra en la página principal del blog. Los cambios se reflejan inmediatamente.</p>

    <form method="POST" action="{{ route('admin.pages.blog.update') }}">
        @method('PUT')
        @csrf

        {{-- ═══════════ HERO DEL BLOG ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Hero del blog</h3>
                <p class="text-xs text-gray-500 mt-1">Textos que aparecen en la cabecera de la página del blog.</p>
            </div>
            <div class="px-6 pb-6 pt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta superior</label>
                    <input type="text" name="hero_label" value="{{ $page->hero_label }}"
                           placeholder="BELLEZA ÁUREA · BLOG"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Texto pequeño sobre el título. Ej: BELLEZA ÁUREA · BLOG</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título (línea 1)</label>
                    <input type="text" name="hero_title" value="{{ $page->hero_title }}"
                           placeholder="Cuida tu visión."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título (línea 2)</label>
                        <input type="text" name="hero_title_line2" value="{{ $page->hero_title_line2 }}"
                               placeholder="Lee, aprende,"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Palabra destacada (azul)</label>
                        <input type="text" name="hero_title_accent" value="{{ $page->hero_title_accent }}"
                               placeholder="protégete."
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Se muestra en color azul al final de la línea 2.</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <input type="text" name="hero_subtitle" value="{{ $page->hero_subtitle }}"
                           placeholder="Consejos, guías y datos respaldados por ciencia para cuidar tu visión en la era digital."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>

        {{-- Preview --}}
        <div class="bg-gray-900 rounded-xl p-8 mb-4 text-center">
            <p class="text-xs font-semibold tracking-widest uppercase mb-4" style="color:#D9B56D;">
                <span x-data x-text="$el.closest('form').querySelector('[name=hero_label]').value || 'BELLEZA ÁUREA · BLOG'">{{ $page->hero_label ?? 'BELLEZA ÁUREA · BLOG' }}</span>
            </p>
            <p class="text-2xl font-bold text-white">Vista previa del hero</p>
            <p class="text-xs text-gray-500 mt-2">Guarda para ver los cambios en la tienda.</p>
        </div>

        {{-- Botón guardar --}}
        <div class="flex justify-end mt-6">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg
                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection
