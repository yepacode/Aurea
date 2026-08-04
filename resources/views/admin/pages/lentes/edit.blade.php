@extends('layouts.admin')

@section('title', 'Editar página del catálogo')
@section('page_title', 'Página del catálogo')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <p class="text-sm text-gray-500 mb-6">Edita el título (H1) y subtítulo que se muestran en la página del catálogo. Los cambios se reflejan inmediatamente en la tienda.</p>

    <form method="POST" action="{{ route('admin.pages.lentes.update') }}">
        @method('PUT')
        @csrf

        {{-- ═══════════ HEADER DEL CATÁLOGO ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Encabezado del catálogo</h3>
                <p class="text-xs text-gray-500 mt-1">Título (H1) y descripción que aparecen en la parte superior del catálogo.</p>
            </div>
            <div class="px-6 pb-6 pt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="catalog_title" value="{{ $page->catalog_title }}"
                           placeholder="Nuestro catálogo"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <input type="text" name="catalog_subtitle" value="{{ $page->catalog_subtitle }}"
                           placeholder="Insumos y cosmética de belleza — uñas, piel, maquillaje y más"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>

        {{-- ═══════════ BENEFICIOS RÁPIDOS ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Beneficios rápidos</h3>
                <p class="text-xs text-gray-500 mt-1">Se muestran debajo del botón "Agregar al carrito" en la ficha de producto y en el checkout. Uno por línea.</p>
            </div>
            <div class="px-6 pb-6 pt-4">
                <textarea name="product_benefits_text" rows="4"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                          placeholder="Envío a toda Colombia&#10;Marcas originales&#10;Precios por mayor y detal&#10;Asesoría personalizada">{{ implode("\n", $page->product_benefits ?? []) }}</textarea>
            </div>
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
