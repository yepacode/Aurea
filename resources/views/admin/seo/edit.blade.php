@extends('layouts.admin')

@section('title', 'SEO — ' . $pageLabel)
@section('page_title', 'SEO — ' . $pageLabel)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
        metaTitle: {{ json_encode($seo->meta_title ?? '') }},
        metaDesc: {{ json_encode($seo->meta_description ?? '') }},
        openGeneral: true,
        openOg: false,
        openTwitter: false,
        openSchema: false,
        titleColor() {
            const len = this.metaTitle.length;
            if (len === 0) return 'text-gray-400';
            if (len <= 60) return 'text-green-600';
            if (len <= 70) return 'text-yellow-600';
            return 'text-red-600';
        },
        descColor() {
            const len = this.metaDesc.length;
            if (len === 0) return 'text-gray-400';
            if (len <= 160) return 'text-green-600';
            if (len <= 180) return 'text-yellow-600';
            return 'text-red-600';
        }
     }">

    <a href="{{ route('admin.seo.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
        &larr; Volver al listado SEO
    </a>

    <form method="POST" action="{{ route('admin.seo.update', $pageKey) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        {{-- ============ SERP PREVIEW ============ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                Vista previa en Google
            </h3>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <div class="max-w-xl">
                    <p class="text-sm truncate" style="color: #1a0dab; font-family: arial, sans-serif; font-size: 20px; line-height: 1.3;"
                       x-text="metaTitle || 'Título de la página'">&nbsp;</p>
                    <p class="text-xs mt-0.5" style="color: #006621; font-family: arial, sans-serif; font-size: 14px;">
                        {{ ['home' => url('/'), 'products-index' => route('products.index'), 'blue-light' => route('blue-light'), 'blog' => route('blog.index'), 'contact' => route('contact'), 'shipping-returns' => route('shipping-returns'), 'quiz' => route('landing.quiz'), 'brands' => route('brands.index')][$pageKey] ?? url('/' . $pageKey) }}
                    </p>
                    <p class="text-sm mt-1" style="color: #545454; font-family: arial, sans-serif; font-size: 14px; line-height: 1.58; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"
                       x-text="metaDesc || 'Descripción de la página...'"></p>
                </div>
            </div>
        </div>

        {{-- ============ SEO GENERAL ============ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="openGeneral = !openGeneral"
                    class="w-full flex items-center justify-between p-6 text-left">
                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    SEO General
                </h3>
                <svg :class="openGeneral && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openGeneral" x-collapse class="px-6 pb-6 space-y-4">
                {{-- Meta Title --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                        <span class="text-xs" :class="titleColor()" x-text="metaTitle.length + '/60 caracteres'"></span>
                    </div>
                    <input type="text" name="meta_title" x-model="metaTitle"
                           value="{{ $seo->meta_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                           placeholder="Título para motores de búsqueda">
                    <p class="mt-1 text-xs text-gray-400">Recomendado: 50-60 caracteres</p>
                </div>

                {{-- Meta Description --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                        <span class="text-xs" :class="descColor()" x-text="metaDesc.length + '/160 caracteres'"></span>
                    </div>
                    <textarea name="meta_description" x-model="metaDesc" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                              placeholder="Descripción para motores de búsqueda">{{ $seo->meta_description }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Recomendado: 120-160 caracteres</p>
                </div>

                {{-- Meta Keywords --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                    <input type="text" name="meta_keywords"
                           value="{{ $seo->meta_keywords }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                           placeholder="palabra1, palabra2, palabra3">
                    <p class="mt-1 text-xs text-gray-400">Separadas por coma. Poco impacto en Google, pero útil para otros buscadores.</p>
                </div>

                {{-- Canonical URL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Canónica</label>
                    <input type="url" name="canonical_url"
                           value="{{ $seo->canonical_url }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                           placeholder="Dejar vacío para usar la URL automática">
                    <p class="mt-1 text-xs text-gray-400">Solo cambiar si esta página tiene contenido duplicado en otra URL.</p>
                </div>

                {{-- Robots --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Robots</label>
                    <div class="flex gap-4">
                        @php
                            $parts = explode(', ', $seo->robots ?? 'index, follow');
                            $indexVal = $parts[0] ?? 'index';
                            $followVal = $parts[1] ?? 'follow';
                        @endphp
                        <select name="robots_index"
                                class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="index" {{ $indexVal === 'index' ? 'selected' : '' }}>index</option>
                            <option value="noindex" {{ $indexVal === 'noindex' ? 'selected' : '' }}>noindex</option>
                        </select>
                        <select name="robots_follow"
                                class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="follow" {{ $followVal === 'follow' ? 'selected' : '' }}>follow</option>
                            <option value="nofollow" {{ $followVal === 'nofollow' ? 'selected' : '' }}>nofollow</option>
                        </select>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Controla si los buscadores indexan y siguen los enlaces de esta página.</p>
                </div>
            </div>
        </div>

        {{-- ============ OPEN GRAPH ============ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="openOg = !openOg"
                    class="w-full flex items-center justify-between p-6 text-left">
                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                    </svg>
                    Open Graph (Facebook, LinkedIn, WhatsApp)
                </h3>
                <svg :class="openOg && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openOg" x-collapse class="px-6 pb-6 space-y-4">
                {{-- OG Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo (og:type)</label>
                    <select name="og_type"
                            class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="website" {{ ($seo->og_type ?? 'website') === 'website' ? 'selected' : '' }}>website</option>
                        <option value="article" {{ ($seo->og_type ?? '') === 'article' ? 'selected' : '' }}>article</option>
                        <option value="product" {{ ($seo->og_type ?? '') === 'product' ? 'selected' : '' }}>product</option>
                    </select>
                </div>

                {{-- OG Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título OG</label>
                    <input type="text" name="og_title"
                           value="{{ $seo->og_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                           placeholder="Dejar vacío para usar el Meta Title">
                </div>

                {{-- OG Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción OG</label>
                    <textarea name="og_description" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                              placeholder="Dejar vacío para usar la Meta Description">{{ $seo->og_description }}</textarea>
                </div>

                {{-- OG Image --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagen OG (1200×630 recomendado)</label>
                    @if($seo->og_image)
                        <div class="mb-2 relative inline-block">
                            <img src="{{ asset('storage/' . $seo->og_image) }}" class="h-32 rounded-lg border border-gray-200 object-cover" alt="OG Image">
                            <label class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center cursor-pointer text-xs hover:bg-red-600">
                                <input type="checkbox" name="remove_og_image" value="1" class="sr-only">
                                &times;
                            </label>
                        </div>
                    @endif
                    <input type="file" name="og_image_file" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
        </div>

        {{-- ============ TWITTER CARD ============ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="openTwitter = !openTwitter"
                    class="w-full flex items-center justify-between p-6 text-left">
                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                    </svg>
                    Twitter Card (X)
                </h3>
                <svg :class="openTwitter && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openTwitter" x-collapse class="px-6 pb-6 space-y-4">
                {{-- Twitter Card Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de tarjeta</label>
                    <select name="twitter_card"
                            class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="summary_large_image" {{ ($seo->twitter_card ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' }}>summary_large_image</option>
                        <option value="summary" {{ ($seo->twitter_card ?? '') === 'summary' ? 'selected' : '' }}>summary</option>
                    </select>
                </div>

                {{-- Twitter Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título Twitter</label>
                    <input type="text" name="twitter_title"
                           value="{{ $seo->twitter_title }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                           placeholder="Dejar vacío para usar el Meta Title">
                </div>

                {{-- Twitter Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción Twitter</label>
                    <textarea name="twitter_description" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                              placeholder="Dejar vacío para usar la Meta Description">{{ $seo->twitter_description }}</textarea>
                </div>

                {{-- Twitter Image --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagen Twitter</label>
                    @if($seo->twitter_image)
                        <div class="mb-2 relative inline-block">
                            <img src="{{ asset('storage/' . $seo->twitter_image) }}" class="h-32 rounded-lg border border-gray-200 object-cover" alt="Twitter Image">
                            <label class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center cursor-pointer text-xs hover:bg-red-600">
                                <input type="checkbox" name="remove_twitter_image" value="1" class="sr-only">
                                &times;
                            </label>
                        </div>
                    @endif
                    <input type="file" name="twitter_image_file" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-400">Si se deja vacío, se usará la imagen OG.</p>
                </div>
            </div>
        </div>

        {{-- ============ SCHEMA.ORG / JSON-LD ============ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <button type="button" @click="openSchema = !openSchema"
                    class="w-full flex items-center justify-between p-6 text-left">
                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/>
                    </svg>
                    Schema.org / JSON-LD personalizado
                </h3>
                <svg :class="openSchema && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSchema" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">JSON-LD adicional</label>
                    <textarea name="custom_schema_markup" rows="10"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                              placeholder='{"{{'@'}}context": "https://schema.org", "{{'@'}}type": "...", ...}'>{{ $seo->custom_schema_markup }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">
                        Se inyectará en el <code class="bg-gray-100 px-1 rounded">&lt;head&gt;</code> como
                        <code class="bg-gray-100 px-1 rounded">&lt;script type="application/ld+json"&gt;</code>.
                        Los schemas automáticos (Organization, FAQ) se mantienen.
                    </p>
                </div>
            </div>
        </div>

        {{-- ============ SUBMIT ============ --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">Cancelar</a>
            <button type="submit"
                    class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                Guardar SEO
            </button>
        </div>

    </form>
</div>
@endsection
