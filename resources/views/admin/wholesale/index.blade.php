@extends('layouts.admin')

@section('title', 'Mayoristas')
@section('page_title', 'Solicitudes de mayoristas')

@php
    $statusMap = [
        'pending'  => ['Pendiente',  '#8A6D1F', '#FBF0D5'],
        'approved' => ['Aprobada',   '#3B7A3B', '#EAF3EA'],
        'rejected' => ['Rechazada',  '#C0554A', '#F8E9E6'],
    ];
    $volumeOptions = config('wholesale.monthly_volume_options', []);
@endphp

@section('content')

    {{-- Contadores --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4">
            <p class="text-xs uppercase tracking-widest text-yellow-700 font-semibold">Pendientes</p>
            <p class="mt-1 text-2xl font-bold text-yellow-900">{{ $counts['pending'] }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4">
            <p class="text-xs uppercase tracking-widest text-green-700 font-semibold">Aprobadas</p>
            <p class="mt-1 text-2xl font-bold text-green-900">{{ $counts['approved'] }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4">
            <p class="text-xs uppercase tracking-widest text-red-700 font-semibold">Rechazadas</p>
            <p class="mt-1 text-2xl font-bold text-red-900">{{ $counts['rejected'] }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, correo, empresa o NIT..."
                   class="sm:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                <option value="">Todos los estados</option>
                @foreach(['pending' => 'Pendientes', 'approved' => 'Aprobadas', 'rejected' => 'Rechazadas'] as $k => $lbl)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $lbl }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filtrar</button>
                <a href="{{ route('admin.wholesale.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($customers->isEmpty())
            <div class="p-8 text-center text-gray-500">Aún no hay solicitudes.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Cliente</th>
                            <th class="px-6 py-3">Empresa</th>
                            <th class="px-6 py-3">Volumen</th>
                            <th class="px-6 py-3">Solicitud</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            @php $s = $statusMap[$customer->wholesaler_status] ?? ['—', '#6B6157', '#EFE7D8']; @endphp
                            <tr class="hover:bg-gray-50" x-data="{ open:false, showReject:false }">
                                <td class="px-6 py-4 align-top">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name ?: '—' }}</div>
                                    <div class="text-xs text-gray-500">
                                        <a href="mailto:{{ $customer->email }}" class="hover:underline">{{ $customer->email }}</a>
                                    </div>
                                    @if($customer->phone)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <a href="tel:{{ $customer->phone }}" class="hover:underline">{{ $customer->phone }}</a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-gray-700">
                                    <div class="font-medium">{{ $customer->wholesaler_company_name ?: '—' }}</div>
                                    @if($customer->wholesaler_nit)
                                        <div class="text-xs text-gray-500 mt-0.5">NIT {{ $customer->wholesaler_nit }}</div>
                                    @endif
                                    @if($customer->city)
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $customer->city }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-gray-700">
                                    {{ $volumeOptions[$customer->wholesaler_monthly_volume] ?? ($customer->wholesaler_monthly_volume ?: '—') }}
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-gray-500">
                                    {{ $customer->wholesaler_requested_at?->format('d/m/Y') ?? '—' }}
                                    @if($customer->wholesaler_approved_at)
                                        <div class="text-xs text-green-700 mt-1">
                                            Aprobada {{ $customer->wholesaler_approved_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                                          style="color:{{ $s[1] }};background:{{ $s[2] }};">
                                        {{ $s[0] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                                    @if($customer->wholesaler_status !== 'approved')
                                        <form method="POST" action="{{ route('admin.wholesale.approve', $customer->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    onclick="return confirm('¿Aprobar como mayorista a {{ $customer->name }}?')"
                                                    class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                                                Aprobar
                                            </button>
                                        </form>
                                    @endif

                                    @if($customer->wholesaler_status !== 'rejected')
                                        <button type="button" @click="showReject = !showReject"
                                                class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors ml-1">
                                            Rechazar
                                        </button>
                                    @endif

                                    @if($customer->wholesaler_notes || ($customer->wholesaler_status === 'pending' && trim((string) $customer->wholesaler_notes) !== ''))
                                        <button type="button" @click="open = !open"
                                                class="inline-flex items-center gap-1 text-gray-500 hover:text-gray-700 text-xs font-semibold px-2 py-1.5 ml-1"
                                                title="Ver comentario">
                                            💬
                                        </button>
                                    @endif

                                    {{-- Panel de rechazo (con notas) --}}
                                    <div x-show="showReject" x-cloak x-transition
                                         class="text-left mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                                        <form method="POST" action="{{ route('admin.wholesale.reject', $customer->id) }}" class="space-y-2">
                                            @csrf @method('PATCH')
                                            <label class="block text-xs font-semibold text-red-800 uppercase tracking-wider">Motivo (lo verá la clienta)</label>
                                            <textarea name="notes" rows="3" required
                                                      placeholder="Ej: Aún no manejamos ventas al por mayor en tu zona."
                                                      class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ $customer->wholesaler_notes }}</textarea>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="showReject = false"
                                                        class="text-xs text-gray-600 hover:text-gray-800 px-3 py-1">Cancelar</button>
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                                    Enviar rechazo
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Comentario de la clienta / motivo si existía --}}
                                    @if($customer->wholesaler_notes)
                                        <div x-show="open" x-cloak class="text-left mt-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                            <p class="text-xs uppercase tracking-widest text-gray-500 font-semibold mb-1">Notas</p>
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $customer->wholesaler_notes }}</p>
                                        </div>
                                    @endif
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
