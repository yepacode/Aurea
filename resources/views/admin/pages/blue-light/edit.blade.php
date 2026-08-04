@extends('layouts.admin')

@section('title', 'Editar página de Rituales')
@section('page_title', 'Página — Rituales')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
        openSection: 'hero',
        toggle(s) { this.openSection = this.openSection === s ? null : s; }
     }">

    <p class="text-sm text-gray-500 mb-6">Edita el <strong>encabezado (H1 + subtítulo)</strong> de la página de Rituales. Los cambios se reflejan inmediatamente en la tienda. <span class="text-amber-600">Nota: solo la sección "Hero" alimenta la página actual de Rituales; las demás secciones son de la versión anterior.</span></p>

    <form method="POST" action="{{ route('admin.pages.blue-light.update') }}">
        @method('PUT')
        @csrf

        {{-- ═══════════ 1. HERO ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('hero')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">1. Hero</h3>
                <svg :class="openSection === 'hero' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'hero'" x-collapse class="px-6 pb-6 space-y-4">
                <p class="text-xs text-gray-400">El título se compone de 3 partes: <strong>prefijo</strong> + <strong>palabra destacada</strong> (en degradado) + <strong>sufijo</strong>.</p>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Prefijo del título</label>
                        <input type="text" name="hero_title_prefix" value="{{ $page->hero_title_prefix }}"
                               placeholder="Rituales de "
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Palabra destacada</label>
                        <input type="text" name="hero_title_accent" value="{{ $page->hero_title_accent }}"
                               placeholder="belleza"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sufijo del título</label>
                        <input type="text" name="hero_title_suffix" value="{{ $page->hero_title_suffix }}"
                               placeholder="?"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <textarea name="hero_subtitle" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                              placeholder="Pequeños gestos que se vuelven costumbre — cuidar tus uñas, tu piel y tu espacio.">{{ $page->hero_subtitle }}</textarea>
                </div>
            </div>
        </div>
        {{-- Botón guardar --}}
        <button type="submit"
                class="w-full py-3 rounded-xl text-white font-medium text-base transition-colors"
                style="background:#D9B56D;"
                onmouseover="this.style.background='#BE9A53'"
                onmouseout="this.style.background='#D9B56D'">
            Guardar cambios
        </button>
    </form>
</div>
@endsection
