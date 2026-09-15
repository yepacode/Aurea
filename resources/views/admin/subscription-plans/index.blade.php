@extends('layouts.admin')
@section('title', 'Planes de suscripción')
@section('page_title', 'Planes de suscripción 🔄')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <p class="text-gray-500">{{ $plans->total() }} plan(es).</p>
    <a href="{{ route('admin.subscription-plans.create') }}" class="inline-flex items-center text-white px-4 py-2 rounded-lg text-sm font-medium"
       style="background:#D9B56D;" onmouseover="this.style.background='#BE9A53'" onmouseout="this.style.background='#D9B56D'">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo plan
    </a>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg p-3 text-sm" style="background:#EAF3EA;color:#3B7A3B;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 rounded-lg p-3 text-sm" style="background:#F8E9E6;color:#C0554A;">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#FBF4E6;">
            <tr style="color:#2E2A26;">
                <th class="text-left px-4 py-3 font-semibold">Plan</th>
                <th class="text-center px-4 py-3 font-semibold">Productos</th>
                <th class="text-center px-4 py-3 font-semibold">Intervalo</th>
                <th class="text-right px-4 py-3 font-semibold">Precio</th>
                <th class="text-right px-4 py-3 font-semibold">Antes</th>
                <th class="text-center px-4 py-3 font-semibold">Suscriptas</th>
                <th class="text-center px-4 py-3 font-semibold">Estado</th>
                <th class="text-right px-4 py-3 font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($plans as $plan)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $plan->name }}</p>
                    <p class="text-xs text-gray-500">/{{ $plan->slug }}</p>
                </td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $plan->items_count }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $plan->interval_days }} días</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800">${{ number_format((float) $plan->base_price, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right text-gray-400 line-through">${{ number_format((float) $plan->regular_price, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center text-gray-600">{{ $plan->subscriptions_count }}</td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="{{ route('admin.subscription-plans.toggle', $plan) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="text-xs px-2 py-1 rounded-full font-medium"
                                style="background:{{ $plan->is_active ? '#EAF3EA' : '#F8E9E6' }};color:{{ $plan->is_active ? '#3B7A3B' : '#C0554A' }};">
                            {{ $plan->is_active ? 'Activo' : 'Inactivo' }}
                        </button>
                    </form>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="text-sm" style="color:#8A6E2E;">Editar</a>
                    <form method="POST" action="{{ route('admin.subscription-plans.destroy', $plan) }}" class="inline ml-2"
                          onsubmit="return confirm('¿Eliminar este plan?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-600">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Aún no hay planes. Crea el primero →</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $plans->links() }}</div>
@endsection
