@extends('layouts.admin')

@section('title', 'NPS · Voz de la clienta')
@section('page_title', 'NPS 📊 — Voz de la clienta')

@php
    // Gradiente rojo → amarillo → verde (mismo que el email/formulario)
    $palette = [
        0  => '#B91C1C', 1  => '#C1272D', 2  => '#D9302B', 3  => '#E85B24',
        4  => '#F0851F', 5  => '#F2B01E', 6  => '#E8C227', 7  => '#C7CE2F',
        8  => '#9CBF3A', 9  => '#5EAE47', 10 => '#2E9E48',
    ];
    $maxCount = max(1, max($distribution));

    // Color del score global (para el "big number")
    $scoreColor = '#6B7280';
    if ($npsScore !== null) {
        $scoreColor = $npsScore >= 50 ? '#2E9E48' : ($npsScore >= 0 ? '#F0851F' : '#B91C1C');
    }
@endphp

@section('content')

    {{-- ── Filtros ─────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.nps.index') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Desde</label>
                <input type="date" name="from" value="{{ $from->toDateString() }}"
                       class="border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-secondary focus:border-secondary">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Hasta</label>
                <input type="date" name="to" value="{{ $to->toDateString() }}"
                       class="border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-secondary focus:border-secondary">
            </div>
            <button type="submit"
                    class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg text-sm">
                Filtrar
            </button>
            @if(request()->has('from') || request()->has('to'))
                <a href="{{ route('admin.nps.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
            @endif
            <div class="ml-auto text-xs text-gray-500">
                Envíos totales (histórico): <strong>{{ $sentTotal }}</strong> ·
                Respuestas: <strong>{{ $respondedTotal }}</strong> ·
                Tasa: <strong>{{ $responseRate }}%</strong>
            </div>
        </div>
    </form>

    {{-- ── KPIs ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        {{-- NPS Score global --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:col-span-1">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">NPS Score</p>
            <p class="mt-2 text-5xl font-bold" style="color: {{ $scoreColor }};">
                {{ $npsScore ?? '—' }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $total }} respuestas en el rango
            </p>
            <p class="mt-2 text-[11px] text-gray-400 leading-tight">
                Promotores% − Detractores%
            </p>
        </div>

        {{-- Promotores --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Promotoras (9–10)</p>
            <p class="mt-2 text-3xl font-bold" style="color:#2E9E48;">{{ $promoterPct }}%</p>
            <p class="mt-1 text-xs text-gray-500">{{ $promoters }} respuestas</p>
            <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div style="width: {{ $promoterPct }}%; background:#2E9E48;" class="h-full"></div>
            </div>
        </div>

        {{-- Pasivas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pasivas (7–8)</p>
            <p class="mt-2 text-3xl font-bold" style="color:#F0851F;">{{ $passivePct }}%</p>
            <p class="mt-1 text-xs text-gray-500">{{ $passives }} respuestas</p>
            <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div style="width: {{ $passivePct }}%; background:#F0851F;" class="h-full"></div>
            </div>
        </div>

        {{-- Detractoras --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Detractoras (0–6)</p>
            <p class="mt-2 text-3xl font-bold" style="color:#B91C1C;">{{ $detractorPct }}%</p>
            <p class="mt-1 text-xs text-gray-500">{{ $detractors }} respuestas</p>
            <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div style="width: {{ $detractorPct }}%; background:#B91C1C;" class="h-full"></div>
            </div>
        </div>
    </div>

    {{-- ── Distribución 0..10 ──────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-800">Distribución de puntuaciones</h2>
            <p class="text-xs text-gray-500">Total: {{ $total }} respuestas</p>
        </div>
        @if($total === 0)
            <p class="text-sm text-gray-500 py-6 text-center">Aún no hay respuestas en el rango seleccionado.</p>
        @else
            <div class="flex items-end gap-2 h-48">
                @foreach(range(0, 10) as $n)
                    @php
                        $count = $distribution[$n];
                        $heightPct = ($count / $maxCount) * 100;
                    @endphp
                    <div class="flex-1 flex flex-col items-center justify-end">
                        <div class="text-[11px] font-semibold text-gray-600 mb-1">{{ $count }}</div>
                        <div style="height: {{ $heightPct }}%; background: {{ $palette[$n] }}; min-height: 4px;"
                             class="w-full rounded-t-md transition-all"></div>
                        <div class="text-xs font-bold text-white mt-1 -mb-6 px-2 py-1 rounded"
                             style="background: {{ $palette[$n] }};">{{ $n }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-10 flex justify-between text-[11px] text-gray-400">
                <span>0 · Detractoras</span>
                <span>6-7 · Pasivas</span>
                <span>9-10 · Promotoras</span>
            </div>
        @endif
    </div>

    {{-- ── Últimas 50 respuestas ───────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-800">Últimas respuestas</h2>
            <p class="text-xs text-gray-500">Máximo 50 · orden por fecha de respuesta</p>
        </div>

        @if($recent->isEmpty())
            <p class="text-sm text-gray-500 py-8 text-center">
                Aún no hay respuestas en este rango. Cuando alguien responda la encuesta, aparecerá aquí.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Fecha</th>
                            <th class="px-5 py-3">Clienta</th>
                            <th class="px-5 py-3">Pedido</th>
                            <th class="px-5 py-3">Score</th>
                            <th class="px-5 py-3">Comentario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recent as $r)
                            @php
                                $bucket = $r->bucket();
                                $color = $bucket === 'promoter' ? '#2E9E48'
                                       : ($bucket === 'passive' ? '#F0851F' : '#B91C1C');
                                $badge = $bucket === 'promoter' ? 'Promotora'
                                       : ($bucket === 'passive' ? 'Pasiva' : 'Detractora');
                            @endphp
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $r->responded_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-800">
                                    @if($r->customer)
                                        <a href="{{ route('admin.customers.show', $r->customer) }}"
                                           class="text-blue-600 hover:underline">
                                            {{ $r->customer->name ?? $r->customer->email }}
                                        </a>
                                        <div class="text-xs text-gray-400">{{ $r->customer->email }}</div>
                                    @else
                                        <span class="text-gray-400">(cliente eliminado)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-sm">
                                    @if($r->order)
                                        <a href="{{ route('admin.orders.show', $r->order) }}"
                                           class="text-blue-600 hover:underline">#{{ $r->order->id }}</a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-bold"
                                          style="background: {{ $color }};">
                                        {{ $r->score }}
                                    </span>
                                    <span class="ml-2 text-xs" style="color: {{ $color }};">{{ $badge }}</span>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-700 max-w-md">
                                    @if($r->comment)
                                        <p class="whitespace-pre-line">{{ $r->comment }}</p>
                                    @else
                                        <span class="text-gray-300 italic">Sin comentario</span>
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
