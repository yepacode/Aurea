@extends('layouts.admin')

@section('title', 'SEO')
@section('page_title', 'SEO')

@section('content')
<div class="max-w-4xl mx-auto">

    <p class="text-sm text-gray-500 mb-6">Configura el SEO de cada página de tu tienda: meta title, description, Open Graph, Twitter Cards y Schema.org.</p>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">Página</th>
                    <th class="px-6 py-3">Meta Title</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pages as $page)
                <tr class="hover:bg-gray-50 {{ ($page['legacy'] ?? false) ? 'opacity-70' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900">{{ $page['label'] }}</span>
                            @if($page['legacy'] ?? false)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide bg-amber-100 text-amber-800" title="Página heredada — ya no está en el menú">
                                    Legacy
                                </span>
                            @endif
                        </div>
                        @if(!empty($page['desc']))
                            <p class="text-xs text-gray-500 mt-0.5">{{ $page['desc'] }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-0.5">{{ $page['key'] }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($page['meta_title'])
                            <span class="text-sm text-gray-700">{{ Str::limit($page['meta_title'], 45) }}</span>
                        @else
                            <span class="text-sm text-gray-400 italic">Sin configurar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($page['configured'])
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Configurado
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Pendiente
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.seo.edit', $page['key']) }}"
                           class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                            </svg>
                            Editar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
