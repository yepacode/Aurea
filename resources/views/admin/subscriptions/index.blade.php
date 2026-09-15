@extends('layouts.admin')
@section('title', 'Suscripciones')
@section('page_title', 'Suscripciones 🔄')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <p class="text-xs uppercase tracking-widest text-gray-500">Suscripciones activas</p>
        <p class="text-3xl font-bold mt-2" style="font-family:'Playfair Display',serif;color:#2E2A26;">{{ $totalActive }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <p class="text-xs uppercase tracking-widest text-gray-500">MRR estimado (mensual)</p>
        <p class="text-3xl font-bold mt-2" style="font-family:'Playfair Display',serif;color:#2E2A26;">
            ${{ number_format((float) $mrr, 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col justify-between">
        <p class="text-xs uppercase tracking-widest text-gray-500">Gestión</p>
        <a href="{{ route('admin.subscription-plans.index') }}" class="mt-2 text-white text-sm px-4 py-2 rounded-lg inline-block text-center" style="background:#D9B56D;">
            Administrar planes →
        </a>
    </div>
</div>

<form method="GET" class="mb-4 flex gap-2 items-end flex-wrap">
    <div>
        <label class="block text-xs text-gray-500">Estado</label>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— Todos —</option>
            @foreach(['active'=>'Activas','paused'=>'Pausadas','cancelled'=>'Canceladas'] as $k=>$v)
                <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500">Buscar cliente</label>
        <input type="text" name="search" value="{{ request('search') }}"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Nombre o email">
    </div>
    <button type="submit" class="text-white text-sm px-4 py-2 rounded-lg" style="background:#2E2A26;">Filtrar</button>
</form>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#FBF4E6;">
            <tr style="color:#2E2A26;">
                <th class="text-left px-4 py-3 font-semibold">Cliente</th>
                <th class="text-left px-4 py-3 font-semibold">Plan</th>
                <th class="text-center px-4 py-3 font-semibold">Estado</th>
                <th class="text-center px-4 py-3 font-semibold">Próxima entrega</th>
                <th class="text-center px-4 py-3 font-semibold">Entregas</th>
                <th class="text-right px-4 py-3 font-semibold">Ingresos*</th>
                <th class="text-right px-4 py-3 font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @php
            $statusMap = ['active'=>['Activa','#3B7A3B','#EAF3EA'],'paused'=>['Pausada','#8A6D1F','#FBF0D5'],'cancelled'=>['Cancelada','#C0554A','#F8E9E6']];
        @endphp
        @forelse($subscriptions as $s)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $s->customer->name }}</p>
                    <p class="text-xs text-gray-500">{{ $s->customer->email }}</p>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ $s->plan->name }}</td>
                <td class="px-4 py-3 text-center">
                    @php $m = $statusMap[$s->status] ?? [$s->status,'#6B6157','#EFE7D8']; @endphp
                    <span class="text-xs px-2 py-1 rounded-full font-medium" style="background:{{ $m[2] }};color:{{ $m[1] }};">{{ $m[0] }}</span>
                </td>
                <td class="px-4 py-3 text-center text-gray-700">
                    {{ $s->status === 'cancelled' ? '—' : $s->next_delivery_at->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-center text-gray-700">{{ $s->total_deliveries }}</td>
                <td class="px-4 py-3 text-right text-gray-800 font-medium">
                    ${{ number_format((float) $s->plan->base_price * $s->total_deliveries, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.subscriptions.show', $s) }}" class="text-sm" style="color:#8A6E2E;">Ver</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No hay suscripciones que coincidan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<p class="mt-2 text-xs text-gray-400">* Ingresos = precio del plan × entregas realizadas.</p>

<div class="mt-4">{{ $subscriptions->links() }}</div>
@endsection
