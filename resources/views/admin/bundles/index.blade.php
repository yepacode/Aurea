@extends('layouts.admin')
@section('title', 'Kits & Bundles')
@section('page_title', 'Kits & Bundles 🎁')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <p class="text-gray-500">{{ $bundles->total() }} kit(s).</p>
    <a href="{{ route('admin.bundles.create') }}" class="inline-flex items-center text-white px-4 py-2 rounded-lg text-sm font-medium"
       style="background:#D9B56D;" onmouseover="this.style.background='#BE9A53'" onmouseout="this.style.background='#D9B56D'">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo kit
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#FBF4E6;">
            <tr style="color:#2E2A26;">
                <th class="text-left px-4 py-3 font-semibold">Imagen</th>
                <th class="text-left px-4 py-3 font-semibold">Nombre</th>
                <th class="text-left px-4 py-3 font-semibold">Productos</th>
                <th class="text-right px-4 py-3 font-semibold">Precio</th>
                <th class="text-right px-4 py-3 font-semibold">Antes</th>
                <th class="text-right px-4 py-3 font-semibold">Ahorro</th>
                <th class="text-center px-4 py-3 font-semibold">Estado</th>
                <th class="text-right px-4 py-3 font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bundles as $bundle)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-3">
                    @if($bundle->image_url)
                        <img src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}"
                             class="w-14 h-14 rounded-lg object-cover" style="border:1px solid rgba(217,181,109,.25);">
                    @else
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center"
                             style="background:#FBF4E6;color:#BE9A53;font-family:'Playfair Display',serif;">
                            🎁
                        </div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $bundle->name }}</p>
                    <p class="text-xs text-gray-500">/{{ $bundle->slug }}</p>
                    @if($bundle->starts_at || $bundle->ends_at)
                        <p class="text-xs mt-1" style="color:#BE9A53;">
                            @if($bundle->starts_at) Desde {{ $bundle->starts_at->format('d M') }} @endif
                            @if($bundle->ends_at) hasta {{ $bundle->ends_at->format('d M Y') }} @endif
                        </p>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $bundle->items_count }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                    ${{ number_format($bundle->price, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-right text-gray-400 line-through">
                    ${{ number_format($bundle->compare_price, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-right">
                    <span class="px-2 py-0.5 rounded text-xs" style="background:#FCEFE6;color:#C97B6B;font-weight:600;">
                        -${{ number_format($bundle->savings, 0, ',', '.') }} ({{ $bundle->savings_percent }}%)
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <form action="{{ route('admin.bundles.toggle', $bundle) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="px-2 py-0.5 rounded text-xs cursor-pointer border-0"
                                style="{{ $bundle->is_active ? 'background:#E8F5E9;color:#2E7D32;' : 'background:#f3f4f6;color:#6b7280;' }}">
                            {{ $bundle->is_active ? 'Activo' : 'Inactivo' }}
                        </button>
                    </form>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('bundles.show', $bundle->slug) }}" target="_blank"
                       class="text-sm text-gray-600 hover:text-gray-900 mr-3">Ver</a>
                    <a href="{{ route('admin.bundles.edit', $bundle) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-3">Editar</a>
                    <form action="{{ route('admin.bundles.destroy', $bundle) }}" method="POST" class="inline"
                          onsubmit="return confirm('¿Eliminar este kit?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                    Todavía no has creado ningún kit.
                    <a href="{{ route('admin.bundles.create') }}" style="color:#BE9A53;text-decoration:underline;">Crea el primero</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $bundles->links() }}</div>
@endsection
