@extends('layouts.admin')

@section('title', 'Cliente: ' . ($customer->name ?: $customer->email))
@section('page_title', 'Cliente: ' . ($customer->name ?: $customer->email))

@section('content')
    @php
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
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
        $totalPaid = $customer->orders()->where('payment_status', 'paid')->sum('total');
        $totalAllOrders = $customer->orders()->sum('total');
    @endphp

    <style>
        .cs-wrap{max-width:100%;min-width:0;}
        .cs-wrap .bg-white{min-width:0;max-width:100%;}
        .cs-wrap .grid > *{min-width:0;}
        .cs-wrap a, .cs-wrap p{overflow-wrap:anywhere;word-break:break-word;}
        @media(max-width:640px){
            .cs-wrap .p-6{padding:16px !important;}
            .cs-wrap table{min-width:520px;}
        }
    </style>
    <div class="cs-wrap max-w-5xl space-y-6">
        <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver al listado</a>

        {{-- Cabecera del cliente --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Nombre</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $customer->name ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Email</p>
                    <a href="mailto:{{ $customer->email }}" class="text-blue-600 hover:underline">{{ $customer->email }}</a>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Teléfono</p>
                    @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:underline">{{ $customer->phone }}</a>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Registrado</p>
                    <p class="text-sm text-gray-700">{{ $customer->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Tiene cuenta</p>
                    @if($customer->hasAccount())
                        <span class="inline-flex items-center gap-1 text-green-700 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            Sí, con contraseña
                        </span>
                    @else
                        <span class="text-sm text-gray-500">Invitado (checkout sin cuenta)</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Ciudad</p>
                    <p class="text-sm text-gray-700">{{ $customer->city ?: '—' }}</p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pedidos totales</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $customer->orders->count() }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-xs text-green-700 uppercase tracking-wide">Total pagado</p>
                    <p class="text-2xl font-semibold text-green-800 mt-1">${{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-xs text-blue-700 uppercase tracking-wide">Suma de todos los pedidos</p>
                    <p class="text-2xl font-semibold text-blue-800 mt-1">${{ number_format($totalAllOrders, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Pedidos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Pedidos recientes</h2>
                <span class="text-xs text-gray-500">Mostrando los últimos {{ $customer->orders->count() }}</span>
            </div>
            @if($customer->orders->isEmpty())
                <div class="p-8 text-center text-gray-500">Este cliente aún no tiene pedidos.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">#</th>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3">Pago</th>
                                <th class="px-6 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($customer->orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-blue-600">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">#{{ $order->id }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3 text-sm font-medium">${{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-500">{{ $order->payment_status }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-blue-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
