@extends('layouts.admin')

@section('title', 'Productos')
@section('page_title', 'Productos')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6"
         x-data="{ salesOpen: false, stockOpen: false, from: '', to: '', threshold: 10 }">
        <p class="text-gray-500">{{ $products->total() }} productos en total.</p>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="salesOpen = true"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    style="background:#FBF4E6;color:#BE9A53;border:1px solid #E8CC92;"
                    onmouseover="this.style.background='#E8CC92';this.style.color='#2E2A26'"
                    onmouseout="this.style.background='#FBF4E6';this.style.color='#BE9A53'"
                    title="CSV con unidades vendidas e ingreso por producto (solo pedidos pagados)">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l4-4 4 4 5-6"/></svg>
                Ventas por producto
            </button>
            <button type="button" @click="stockOpen = true"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    style="background:#FEE9E5;color:#B04A2E;border:1px solid #F1B9A9;"
                    onmouseover="this.style.background='#F1B9A9';this.style.color='#2E2A26'"
                    onmouseout="this.style.background='#FEE9E5';this.style.color='#B04A2E'"
                    title="Productos activos con stock por debajo del umbral">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                Inventario bajo
            </button>

            {{-- Modal Ventas por producto --}}
            <div x-show="salesOpen" x-cloak @keydown.escape.window="salesOpen = false"
                 class="fixed inset-0 z-[70] flex items-center justify-center px-4"
                 style="background:rgba(20,17,13,0.55);">
                <div @click.outside="salesOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Ventas por producto (CSV)</h3>
                            <p class="text-xs text-gray-500 mt-1">Solo cuenta pedidos con pago aprobado.</p>
                        </div>
                        <button type="button" @click="salesOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block"><span class="text-xs font-medium text-gray-600">Desde</span>
                            <input type="date" x-model="from" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></label>
                        <label class="block"><span class="text-xs font-medium text-gray-600">Hasta</span>
                            <input type="date" x-model="to" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></label>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-2">
                        <button type="button" @click="salesOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
                        <button type="button"
                                @click="
                                    let p = new URLSearchParams();
                                    if (from) p.set('from', from);
                                    if (to) p.set('to', to);
                                    let qs = p.toString();
                                    window.location = '{{ route('admin.reports.sales-by-product') }}' + (qs ? ('?' + qs) : '');
                                    salesOpen = false;
                                "
                                class="inline-flex items-center px-5 py-2 rounded-lg text-sm font-semibold text-white"
                                style="background:#D9B56D;"
                                onmouseover="this.style.background='#BE9A53'"
                                onmouseout="this.style.background='#D9B56D'">Descargar CSV</button>
                    </div>
                </div>
            </div>

            {{-- Modal Inventario bajo --}}
            <div x-show="stockOpen" x-cloak @keydown.escape.window="stockOpen = false"
                 class="fixed inset-0 z-[70] flex items-center justify-center px-4"
                 style="background:rgba(20,17,13,0.55);">
                <div @click.outside="stockOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Inventario bajo (CSV)</h3>
                            <p class="text-xs text-gray-500 mt-1">Productos activos con stock por debajo del umbral.</p>
                        </div>
                        <button type="button" @click="stockOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg></button>
                    </div>
                    <label class="block"><span class="text-xs font-medium text-gray-600">Umbral (stock ≤)</span>
                        <input type="number" min="0" max="9999" x-model.number="threshold" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></label>
                    <div class="mt-6 flex items-center justify-end gap-2">
                        <button type="button" @click="stockOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
                        <button type="button"
                                @click="
                                    let p = new URLSearchParams();
                                    if (threshold !== '' && threshold !== null) p.set('threshold', threshold);
                                    let qs = p.toString();
                                    window.location = '{{ route('admin.reports.low-stock') }}' + (qs ? ('?' + qs) : '');
                                    stockOpen = false;
                                "
                                class="inline-flex items-center px-5 py-2 rounded-lg text-sm font-semibold text-white"
                                style="background:#D9B56D;"
                                onmouseover="this.style.background='#BE9A53'"
                                onmouseout="this.style.background='#D9B56D'">Descargar CSV</button>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.products.export') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
               style="background:#FFFFFF;color:#6B6157;border:1px solid #D1C7BC;"
               onmouseover="this.style.background='#FBF8F2';this.style.color='#2E2A26'"
               onmouseout="this.style.background='#FFFFFF';this.style.color='#6B6157'"
               title="Descarga un Excel con todos los productos actuales">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                Exportar
            </a>
            <a href="{{ route('admin.products.import-images') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
               style="background:#F0F2EB;color:#8A9680;border:1px solid #A8B29A;"
               onmouseover="this.style.background='#A8B29A';this.style.color='#FFFFFF'"
               onmouseout="this.style.background='#F0F2EB';this.style.color='#8A9680'">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                Importar imágenes
            </a>
            <a href="{{ route('admin.products.import') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
               style="background:#FBF4E6;color:#BE9A53;border:1px solid #E8CC92;"
               onmouseover="this.style.background='#E8CC92';this.style.color='#2E2A26'"
               onmouseout="this.style.background='#FBF4E6';this.style.color='#BE9A53'">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Importar Excel
            </a>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
               style="background:#D9B56D;"
               onmouseover="this.style.background='#BE9A53'"
               onmouseout="this.style.background='#D9B56D'">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo producto
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filtrar</button>
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            </div>
        </div>
    </form>

    {{-- Products table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($products->isEmpty())
            <div class="p-8 text-center text-gray-500">No se encontraron productos.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Producto</th>
                            <th class="px-6 py-3">Categoría</th>
                            <th class="px-6 py-3">Precio</th>
                            <th class="px-6 py-3">Stock</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($product->images && count($product->images) > 0)
                                                <img src="{{ asset('storage/' . $product->images[0]) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3 3h18"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.products.show', $product) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $product->name }}</a>
                                            @if($product->is_featured)
                                                <span class="ml-1 text-xs text-yellow-600">★</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $product->category->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="font-medium">${{ number_format($product->price, 0, ',', '.') }}</span>
                                    @if($product->compare_price)
                                        <span class="text-gray-400 line-through text-xs ml-1">${{ number_format($product->compare_price, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                @php
                                    $totalStock = $product->availableStock();
                                    $hasVariants = $product->variants->where('is_active', true)->count() > 0;
                                @endphp
                                <td class="px-6 py-4 text-sm {{ $totalStock < 10 ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                    {{ $totalStock }}
                                    @if($hasVariants)
                                        <span class="text-xs text-gray-400 font-normal block">({{ $product->variants->where('is_active', true)->count() }} variantes)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.products.toggle', $product) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                                            {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-gray-500 hover:text-blue-600" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-600" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
