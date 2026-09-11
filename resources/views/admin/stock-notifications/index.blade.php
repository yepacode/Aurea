@extends('layouts.admin')

@section('title', 'Avisos de stock')
@section('page_title', 'Avisos de stock — quiénes esperan qué')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <p class="text-gray-500">{{ $notifications->total() }} avisos en total.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($notifications->isEmpty())
            <div class="p-8 text-center text-gray-500">Nadie está esperando ningún producto por el momento.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Producto</th>
                            <th class="px-6 py-3">Correo</th>
                            <th class="px-6 py-3">¿Notificado?</th>
                            <th class="px-6 py-3">Solicitado</th>
                            <th class="px-6 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($notifications as $n)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    @if($n->product)
                                        <a href="{{ route('admin.products.edit', $n->product) }}" class="text-blue-600 hover:underline">
                                            {{ $n->product->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">(producto eliminado)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600">
                                    <a href="mailto:{{ $n->email }}" class="hover:underline">{{ $n->email }}</a>
                                </td>
                                <td class="px-6 py-3">
                                    @if($n->notified_at)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                            {{ $n->notified_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    {{ $n->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.stock-notifications.destroy', $n) }}"
                                          onsubmit="return confirm('¿Eliminar este aviso?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                @if(view()->exists('pagination.aurea'))
                    {{ $notifications->links('pagination.aurea') }}
                @else
                    {{ $notifications->links() }}
                @endif
            </div>
        @endif
    </div>
@endsection
