@extends('layouts.admin')

@section('title', 'Clientes')
@section('page_title', 'Clientes')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6"
         x-data="{ loyaltyOpen: false, from: '', to: '', type: '' }">
        <p class="text-gray-500">{{ $customers->total() }} clientes en total.</p>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.reports.customers') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
               style="background:#FBF4E6;color:#BE9A53;border:1px solid #E8CC92;"
               onmouseover="this.style.background='#E8CC92';this.style.color='#2E2A26'"
               onmouseout="this.style.background='#FBF4E6';this.style.color='#BE9A53'"
               title="CSV con datos de contacto, compras totales y puntos">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Exportar clientes
            </a>
            <button type="button" @click="loyaltyOpen = true"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    style="background:#F0F2EB;color:#8A9680;border:1px solid #A8B29A;"
                    onmouseover="this.style.background='#A8B29A';this.style.color='#FFFFFF'"
                    onmouseout="this.style.background='#F0F2EB';this.style.color='#8A9680'"
                    title="Historial de puntos ganados, canjeados y expirados">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                Puntos fidelización
            </button>

            {{-- Modal loyalty --}}
            <div x-show="loyaltyOpen" x-cloak @keydown.escape.window="loyaltyOpen = false"
                 class="fixed inset-0 z-[70] flex items-center justify-center px-4"
                 style="background:rgba(20,17,13,0.55);">
                <div @click.outside="loyaltyOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Movimientos de puntos (CSV)</h3>
                            <p class="text-xs text-gray-500 mt-1">Historial cronológico por cliente.</p>
                        </div>
                        <button type="button" @click="loyaltyOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block"><span class="text-xs font-medium text-gray-600">Desde</span>
                            <input type="date" x-model="from" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></label>
                        <label class="block"><span class="text-xs font-medium text-gray-600">Hasta</span>
                            <input type="date" x-model="to" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></label>
                        <label class="block col-span-2"><span class="text-xs font-medium text-gray-600">Tipo</span>
                            <select x-model="type" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="">Todos</option>
                                <option value="earned">Ganados</option>
                                <option value="redeemed">Canjeados</option>
                                <option value="expired">Expirados</option>
                                <option value="adjusted">Ajustados</option>
                            </select>
                        </label>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-2">
                        <button type="button" @click="loyaltyOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
                        <button type="button"
                                @click="
                                    let p = new URLSearchParams();
                                    if (from) p.set('from', from);
                                    if (to) p.set('to', to);
                                    if (type) p.set('type', type);
                                    let qs = p.toString();
                                    window.location = '{{ route('admin.reports.loyalty') }}' + (qs ? ('?' + qs) : '');
                                    loyaltyOpen = false;
                                "
                                class="inline-flex items-center px-5 py-2 rounded-lg text-sm font-semibold text-white"
                                style="background:#D9B56D;"
                                onmouseover="this.style.background='#BE9A53'"
                                onmouseout="this.style.background='#D9B56D'">Descargar CSV</button>
                    </div>
                </div>
            </div>
        </div>
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
