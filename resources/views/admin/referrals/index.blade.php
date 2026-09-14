@extends('layouts.admin')

@section('title', 'Referidas')
@section('page_title', 'Programa Recomienda y gana')

@section('content')
    {{-- Métricas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Total referidas</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">{{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Completadas</p>
            <p class="mt-2 text-3xl font-bold text-green-700">{{ number_format($completed, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Pendientes</p>
            <p class="mt-2 text-3xl font-bold text-yellow-700">{{ number_format($pending, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Tasa de conversión</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">{{ number_format($rate, 1, ',', '.') }}%</p>
        </div>
    </div>

    @if($topReferrer)
        <div class="bg-gradient-to-br from-yellow-50 to-amber-100 border border-amber-300 rounded-xl p-5 mb-6">
            <p class="text-xs uppercase tracking-wider text-amber-800 font-semibold">🏆 Top referrer</p>
            <p class="mt-2 text-lg font-bold text-amber-900">
                {{ $topReferrer->name ?: 'Sin nombre' }}
                <span class="text-sm text-amber-700 font-normal">({{ $topReferrer->email }})</span>
            </p>
            <p class="mt-1 text-sm text-amber-800">
                {{ $topReferrer->completed_count }} amigas convertidas · código
                <code class="bg-white px-2 py-0.5 rounded text-amber-900 font-mono">{{ $topReferrer->referral_code }}</code>
            </p>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-800">Últimas 50 referidas</h2>
        </div>
        @if($referrals->isEmpty())
            <div class="p-8 text-center text-gray-500">Aún no hay registros de referidas.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3">Recomendó</th>
                            <th class="px-6 py-3">Referida</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Orden</th>
                            <th class="px-6 py-3 text-right">Puntos otorgados</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @foreach($referrals as $r)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap text-gray-600">
                                    {{ $r->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800">{{ $r->referrer->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $r->referrer->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800">{{ $r->referred->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $r->referred->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    @if($r->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Completada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($r->first_order_id)
                                        <a href="{{ route('admin.orders.show', $r->first_order_id) }}"
                                           class="text-blue-600 hover:underline font-medium">
                                            #{{ $r->first_order_id }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right whitespace-nowrap font-semibold">
                                    @if($r->status === 'completed')
                                        <span class="text-green-700">
                                            +{{ number_format($r->reward_referrer_points + $r->reward_referred_points, 0, ',', '.') }} ⭐
                                        </span>
                                        <div class="text-xs text-gray-500 font-normal">
                                            ({{ $r->reward_referrer_points }} + {{ $r->reward_referred_points }})
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
