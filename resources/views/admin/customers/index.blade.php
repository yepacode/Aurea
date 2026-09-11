@extends('layouts.admin')

@section('title', 'Clientes')
@section('page_title', 'Clientes')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <p class="text-gray-500">{{ $customers->total() }} clientes en total.</p>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o email..."
                   class="sm:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Buscar</button>
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($customers->isEmpty())
            <div class="p-8 text-center text-gray-500">Aún no hay clientes registrados.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Cliente</th>
                            <th class="px-6 py-3">Teléfono</th>
                            <th class="px-6 py-3 text-center">Pedidos</th>
                            <th class="px-6 py-3 text-right">Total gastado</th>
                            <th class="px-6 py-3 text-center">Cuenta</th>
                            <th class="px-6 py-3">Registrado</th>
                            <th class="px-6 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            @php
                                $totalPaid = $customer->orders()->where('payment_status', 'paid')->sum('total');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name ?: '—' }}</div>
                                    <div class="text-xs text-gray-500">
                                        <a href="mailto:{{ $customer->email }}" class="hover:underline">{{ $customer->email }}</a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}" class="hover:underline">{{ $customer->phone }}</a>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-semibold {{ $customer->orders_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $customer->orders_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    ${{ number_format($totalPaid, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($customer->hasAccount())
                                        <span class="inline-flex items-center gap-1 text-green-600" title="Tiene cuenta con contraseña">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="text-gray-300" title="Invitado (sin cuenta)">–</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $customer->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-sm text-blue-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                @if(view()->exists('pagination.aurea'))
                    {{ $customers->links('pagination.aurea') }}
                @else
                    {{ $customers->links() }}
                @endif
            </div>
        @endif
    </div>
@endsection
