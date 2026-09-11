@extends('layouts.admin')

@section('title', 'Pedidos')
@section('page_title', 'Pedidos')

@section('content')
    @php
        $statusColors = [
            'pending'   => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'shipped'   => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
        $statusLabels = [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'shipped' => 'Enviada',
            'delivered' => 'Entregada',
            'cancelled' => 'Cancelada',
        ];
        $payColors = [
            'paid'       => 'bg-green-100 text-green-800',
            'pending'    => 'bg-amber-100 text-amber-800',
            'processing' => 'bg-amber-100 text-amber-800',
            'failed'     => 'bg-red-100 text-red-800',
            'refunded'   => 'bg-gray-200 text-gray-700',
        ];
        $payLabels = [
            'paid' => 'Pagado',
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'failed' => 'Fallido',
            'refunded' => 'Reembolsado',
        ];
        $methodLabels = [
            'card' => ['Tarjeta', '💳'],
            'transfer' => ['Transferencia', '🏦'],
            'cash_on_delivery' => ['Contra entrega', '📦'],
            'epayco' => ['ePayco', '⚡'],
        ];
    @endphp

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <p class="text-gray-500">{{ $orders->total() }} pedidos en total.</p>
        <a href="{{ route('admin.orders.export') }}" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            Exportar CSV
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por ID, nombre o email..."
                   class="sm:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                @foreach($statusLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los pagos</option>
                @foreach($payLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('payment_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="sm:col-span-4 flex gap-2 justify-end">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">Filtrar</button>
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-8 text-center text-gray-500">No se encontraron pedidos.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3">Método</th>
                            <th class="px-4 py-3">Pago</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Ref. ePayco</th>
                            <th class="px-4 py-3">Ciudad</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orders as $order)
                            @php
                                $sc = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                $pc = $payColors[$order->payment_status] ?? 'bg-gray-100 text-gray-700';
                                $method = $methodLabels[$order->payment_method] ?? [$order->payment_method ?? '—', '•'];
                                $city = $order->shipping_address ? trim(\Illuminate\Support\Str::before($order->shipping_address, ',')) : null;
                            @endphp
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-4 py-3 text-sm font-medium whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">#{{ $order->id }}</a>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->customer?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->customer?->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium whitespace-nowrap">${{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                                    <span class="mr-1">{{ $method[1] }}</span>{{ $method[0] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pc }}">
                                        {{ $payLabels[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sc }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($order->payment_reference)
                                        <code class="bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 text-amber-900 font-mono select-all" title="Referencia de la pasarela — úsala para reclamos">{{ $order->payment_reference }}</code>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $city ?: '—' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-blue-600 hover:underline">Ver</a>
                                        @if($order->tracking_token)
                                            <a href="{{ route('order.track', $order->tracking_token) }}" target="_blank" rel="noopener"
                                               class="text-sm text-gray-600 hover:text-gray-900 hover:underline" title="Link público de seguimiento para el cliente">Seguimiento</a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                                              onsubmit="return confirm('¿Eliminar el pedido #{{ $order->id }}? Esta acción no se puede deshacer.')" class="inline">
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
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
