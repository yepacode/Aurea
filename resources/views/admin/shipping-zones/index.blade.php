@extends('layouts.admin')

@section('title', 'Zonas de envío')
@section('page_title', 'Zonas de envío')

@section('content')
<div class="max-w-5xl space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Zonas y transportadoras</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Cada zona agrupa departamentos y define transportadora, tarifa base, tarifa por kg extra y tiempo estimado.
                    Al llegar al checkout el cliente ve el costo automático según el departamento que escoja.
                </p>
            </div>
            <a href="{{ route('admin.shipping-zones.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nueva zona
            </a>
        </div>

        @if($zones->isEmpty())
            <div class="p-10 text-center text-gray-500">
                <p class="text-sm">Aún no has creado zonas de envío.</p>
                <p class="text-xs mt-1">Mientras no haya zonas, los pedidos usarán la tarifa fallback (config <code>shipping.fallback_cost</code>).</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Orden</th>
                        <th class="px-6 py-3">Zona</th>
                        <th class="px-6 py-3">Transportadora</th>
                        <th class="px-6 py-3">Departamentos</th>
                        <th class="px-6 py-3">Costo base</th>
                        <th class="px-6 py-3">Kg extra</th>
                        <th class="px-6 py-3">Entrega</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($zones as $zone)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $zone->sort_order }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900">{{ $zone->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $carriers[$zone->carrier] ?? ucfirst($zone->carrier) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @php $deps = $zone->departments->pluck('department'); @endphp
                            @if($deps->isEmpty())
                                <span class="text-xs italic text-gray-400">Sin departamentos</span>
                            @else
                                <span class="text-xs text-gray-500">{{ $deps->count() }} depto{{ $deps->count() === 1 ? '' : 's' }} · </span>
                                <span class="text-xs">{{ $deps->take(3)->implode(', ') }}@if($deps->count() > 3), +{{ $deps->count() - 3 }}@endif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium tabular-nums">
                            ${{ number_format($zone->base_cost, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 tabular-nums">
                            @if($zone->extra_kg_cost > 0)
                                +${{ number_format($zone->extra_kg_cost, 0, ',', '.') }}/kg
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($zone->delivery_days_min === $zone->delivery_days_max)
                                {{ $zone->delivery_days_min }} día{{ $zone->delivery_days_min === 1 ? '' : 's' }}
                            @else
                                {{ $zone->delivery_days_min }}–{{ $zone->delivery_days_max }} días
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($zone->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Activa</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.shipping-zones.edit', $zone) }}"
                                   class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.shipping-zones.destroy', $zone) }}"
                                      onsubmit="return confirm('¿Eliminar la zona {{ $zone->name }}? Los pedidos existentes conservan su información.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
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

    <div class="text-xs text-gray-500">
        <p><strong>Regla de resolución:</strong> se toma la primera zona <em>activa</em> ordenada por <em>Orden</em> (menor a mayor) que contenga el departamento del cliente. Si un departamento está en varias zonas, la de menor <em>Orden</em> gana.</p>
        <p class="mt-1">El umbral global de <em>envío gratis</em> se gestiona en <a href="{{ route('admin.shipping.index') }}" class="text-blue-600 hover:underline">Envíos → Configuración general</a>.</p>
    </div>
</div>
@endsection
