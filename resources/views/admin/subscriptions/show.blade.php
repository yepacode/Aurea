@extends('layouts.admin')
@section('title', 'Suscripción #' . $subscription->id)
@section('page_title', 'Suscripción #' . $subscription->id)

@section('content')
@php
    $statusMap = ['active'=>['Activa','#3B7A3B','#EAF3EA'],'paused'=>['Pausada','#8A6D1F','#FBF0D5'],'cancelled'=>['Cancelada','#C0554A','#F8E9E6']];
    $m = $statusMap[$subscription->status] ?? [$subscription->status,'#6B6157','#EFE7D8'];
    $addr = $subscription->shipping_address_json ?? [];
@endphp

@if(session('success'))
    <div class="mb-4 rounded-lg p-3 text-sm" style="background:#EAF3EA;color:#3B7A3B;">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex justify-between items-start gap-4 flex-wrap">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-500">Cliente</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $subscription->customer->name }}</p>
                    <p class="text-sm text-gray-500">{{ $subscription->customer->email }} · {{ $subscription->customer->phone ?? 's/tel' }}</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full font-medium" style="background:{{ $m[2] }};color:{{ $m[1] }};">{{ $m[0] }}</span>
            </div>

            <hr class="my-4">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div><p class="text-xs text-gray-500">Plan</p><p class="font-medium">{{ $subscription->plan->name }}</p></div>
                <div><p class="text-xs text-gray-500">Intervalo</p><p class="font-medium">Cada {{ $subscription->plan->interval_days }} días</p></div>
                <div><p class="text-xs text-gray-500">Próxima entrega</p><p class="font-medium">{{ $subscription->status === 'cancelled' ? '—' : $subscription->next_delivery_at->format('d M Y') }}</p></div>
                <div><p class="text-xs text-gray-500">Total entregas</p><p class="font-medium">{{ $subscription->total_deliveries }}</p></div>
                <div><p class="text-xs text-gray-500">Método de pago</p><p class="font-medium">{{ $subscription->payment_method === 'epayco' ? 'ePayco' : 'Contra entrega' }}</p></div>
                <div><p class="text-xs text-gray-500">Precio</p><p class="font-medium">${{ number_format((float) $subscription->plan->base_price, 0, ',', '.') }}</p></div>
                <div><p class="text-xs text-gray-500">Creada</p><p class="font-medium">{{ $subscription->created_at->format('d M Y') }}</p></div>
                <div><p class="text-xs text-gray-500">Cancelada</p><p class="font-medium">{{ optional($subscription->cancelled_at)->format('d M Y') ?? '—' }}</p></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold mb-3" style="font-family:'Playfair Display',serif;">Historial de entregas</h2>
            @if($subscription->deliveries->isEmpty())
                <p class="text-sm text-gray-500">Sin entregas todavía.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                            <th class="py-2">Fecha</th><th>Pedido</th><th>Estado</th><th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscription->deliveries as $d)
                            <tr class="border-t border-gray-100">
                                <td class="py-2">{{ optional($d->generated_at ?? $d->scheduled_at)->format('d M Y H:i') }}</td>
                                <td>
                                    @if($d->order)<a href="{{ route('admin.orders.show', $d->order) }}" style="color:#8A6E2E;">#{{ $d->order->id }}</a>@else — @endif
                                </td>
                                <td>{{ ucfirst($d->status) }}</td>
                                <td class="text-gray-500">{{ $d->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold mb-3" style="font-family:'Playfair Display',serif;">Acciones</h2>
            <div class="flex flex-col gap-2">
                @if($subscription->status === 'active')
                    <form method="POST" action="{{ route('admin.subscriptions.pause', $subscription) }}">
                        @csrf @method('PATCH')
                        <input type="text" name="pause_reason" placeholder="Motivo (opcional)" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm mb-2">
                        <button type="submit" class="w-full text-sm px-3 py-2 rounded-lg" style="background:#FBF0D5;color:#8A6D1F;">Pausar</button>
                    </form>
                @elseif($subscription->status === 'paused')
                    <form method="POST" action="{{ route('admin.subscriptions.resume', $subscription) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full text-sm px-3 py-2 rounded-lg" style="background:#EAF3EA;color:#3B7A3B;">Reanudar</button>
                    </form>
                @endif
                @if($subscription->status !== 'cancelled')
                    <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription) }}"
                          onsubmit="return confirm('¿Cancelar esta suscripción?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full text-sm px-3 py-2 rounded-lg" style="background:#F8E9E6;color:#C0554A;">Cancelar</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold mb-3" style="font-family:'Playfair Display',serif;">Dirección de entrega</h2>
            @if(!empty($addr))
                <p class="text-sm text-gray-700">{{ $addr['name'] ?? '' }}</p>
                <p class="text-sm text-gray-500">{{ $addr['phone'] ?? '' }}</p>
                <p class="text-sm text-gray-700 mt-2">{{ $addr['address'] ?? '' }}</p>
                <p class="text-sm text-gray-500">{{ implode(', ', array_filter([$addr['city'] ?? null, $addr['state'] ?? null, $addr['zip_code'] ?? null])) }}</p>
            @else
                <p class="text-sm text-gray-500">Sin dirección específica — usa los datos del perfil del cliente.</p>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold mb-3" style="font-family:'Playfair Display',serif;">Productos del plan</h2>
            <ul class="text-sm text-gray-700 space-y-1">
                @foreach($subscription->plan->products as $p)
                    <li>• {{ $p->name }} <span class="text-gray-400">× {{ $p->pivot->quantity }}</span></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
