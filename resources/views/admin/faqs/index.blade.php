@extends('layouts.admin')

@section('title', 'FAQs')
@section('page_title', 'FAQs (Chatbot)')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <p class="text-gray-500 text-sm">{{ $faqs->count() }} preguntas mostradas.</p>
            <form method="GET" action="{{ route('admin.faqs.index') }}" class="flex items-center gap-2">
                <label for="category" class="text-sm text-gray-600">Categoría:</label>
                <select name="category" id="category" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $categoryId === $c->id ? 'selected' : '' }}>
                            {{ $c->emoji }} {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.faq-categories.index') }}"
               class="inline-flex items-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Categorías
            </a>
            <a href="{{ route('admin.faqs.create') }}"
               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nueva FAQ
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($faqs->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <p>No hay FAQs {{ $categoryId ? 'en esta categoría' : 'todavía' }}.</p>
                <p class="text-sm mt-1">
                    <a href="{{ route('admin.faqs.create') }}" class="text-blue-600 hover:underline">Crea la primera pregunta</a>.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Categoría</th>
                        <th class="px-4 py-3">Pregunta</th>
                        <th class="px-4 py-3 text-center">Vistas</th>
                        <th class="px-4 py-3 text-center">Útil</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Orden</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($faqs as $faq)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-800 text-xs font-medium">
                                <span>{{ $faq->category->emoji }}</span>
                                <span>{{ $faq->category->name }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium max-w-md">
                            {{ Str::limit($faq->question, 90) }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $faq->view_count }}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $faq->helpful_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($faq->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Activa</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $faq->sort_order }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.faqs.edit', $faq) }}"
                                   class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}"
                                      onsubmit="return confirm('¿Eliminar esta FAQ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
@endsection
