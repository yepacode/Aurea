@extends('layouts.admin')

@section('title', 'Categorías de FAQs')
@section('page_title', 'Categorías de FAQs')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <p class="text-gray-500 text-sm">{{ $categories->count() }} categorías en total.</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.faqs.index') }}"
               class="inline-flex items-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Volver a FAQs
            </a>
            <a href="{{ route('admin.faq-categories.create') }}"
               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nueva categoría
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($categories->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p>Sin categorías. Crea la primera para empezar a agregar preguntas.</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Emoji</th>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3 text-center">FAQs</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Orden</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($categories as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-2xl leading-none">{{ $c->emoji ?: '💬' }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $c->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $c->slug }}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $c->faqs_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($c->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Activa</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $c->sort_order }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.faq-categories.edit', $c) }}"
                                   class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.faq-categories.destroy', $c) }}"
                                      onsubmit="return confirm('¿Eliminar esta categoría? Solo si no tiene preguntas asociadas.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9M18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
