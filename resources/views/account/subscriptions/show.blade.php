@extends('layouts.app')

@section('title', 'Detalle de suscripción | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $statusMap = [
        'active'    => ['Activa',    '#3B7A3B', '#EAF3EA'],
        'paused'    => ['Pausada',   '#8A6D1F', '#FBF0D5'],
        'cancelled' => ['Cancelada', '#C0554A', '#F8E9E6'],
    ];
    $delStatusMap = [
        'pending'   => ['Programada', '#8A6D1F', '#FBF0D5'],
        'generated' => ['Generada',   '#3B7A3B', '#EAF3EA'],
        'delivered' => ['Entregada',  '#3B7A3B', '#EAF3EA'],
        'skipped'   => ['Omitida',    '#6B6157', '#EFE7D8'],
        'failed'    => ['Fallida',    '#C0554A', '#F8E9E6'],
    ];
    $addr = $subscription->shipping_address_json ?? [];
@endphp
<section style="padding:clamp(32px,5vw,60px) 20px;background:#FBF8F2;min-height:60vh;">
    <div style="max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap;gap:26px;align-items:flex-start;">
        <aside style="flex:1 1 240px;max-width:280px;">@include('account._nav')</aside>

        <div style="flex:2 1 620px;min-width:0;">
            <p style="margin:0 0 4px;"><a href="{{ route('account.subscriptions') }}" style="color:#8B7B5C;text-decoration:none;font-size:13px;">← Mis suscripciones</a></p>
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3vw,32px);color:#2E2A26;margin:0 0 12px;">
                {{ $subscription->plan->name }}
                @php $s = $statusMap[$subscription->status] ?? [$subscription->status,'#6B6157','#EFE7D8']; @endphp
                <span style="display:inline-block;padding:4px 12px;border-radius:9999px;font-size:12px;font-weight:600;color:{{ $s[1] }};background:{{ $s[2] }};margin-left:8px;vertical-align:middle;">{{ $s[0] }}</span>
            </h1>

            @if(session('success'))
                <div style="background:#EAF3EA;color:#3B7A3B;padding:12px 16px;border-radius:12px;margin-bottom:18px;">{{ session('success') }}</div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:22px;">
                <div style="background:#fff;border:1px solid #E5DCC9;border-radius:14px;padding:16px 18px;">
                    <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#8B7B5C;">Próxima entrega</p>
                    <p style="margin:6px 0 0;font-weight:600;color:#2E2A26;">
                        {{ $subscription->status === 'cancelled' ? '—' : $subscription->next_delivery_at->translatedFormat('l j \d\e F') }}
                    </p>
                </div>
                <div style="background:#fff;border:1px solid #E5DCC9;border-radius:14px;padding:16px 18px;">
                    <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#8B7B5C;">Total entregas</p>
                    <p style="margin:6px 0 0;font-weight:600;color:#2E2A26;">{{ $subscription->total_deliveries }}</p>
                </div>
                <div style="background:#fff;border:1px solid #E5DCC9;border-radius:14px;padding:16px 18px;">
                    <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#8B7B5C;">Ritual</p>
                    <p style="margin:6px 0 0;font-weight:600;color:#2E2A26;">${{ number_format((float) $subscription->plan->base_price, 0, ',', '.') }} / {{ $subscription->plan->interval_days }} días</p>
                </div>
            </div>

            <section style="background:#fff;border:1px solid #E5DCC9;border-radius:16px;padding:22px 24px;margin-bottom:18px;">
                <h2 style="font-family:'Playfair Display',serif;font-size:20px;color:#2E2A26;margin:0 0 12px;">Productos incluidos</h2>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach($subscription->plan->products as $p)
                        <li style="padding:8px 0;border-bottom:1px dashed #EFE7D8;font-size:14px;color:#3B310F;">
                            {{ $p->name }} <span style="color:#8B7B5C;">× {{ $p->pivot->quantity }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section style="background:#fff;border:1px solid #E5DCC9;border-radius:16px;padding:22px 24px;margin-bottom:18px;">
                <h2 style="font-family:'Playfair Display',serif;font-size:20px;color:#2E2A26;margin:0 0 12px;">Dirección de entrega</h2>
                <form method="POST" action="{{ route('account.subscriptions.address', $subscription) }}">
                    @csrf @method('PUT')
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                        <div><label style="font-size:12px;color:#8B7B5C;">Nombre</label><input type="text" name="name" value="{{ $addr['name'] ?? auth('customer')->user()->name }}" required style="width:100%;padding:10px;border:1px solid #E5DCC9;border-radius:8px;"></div>
                        <div><label style="font-size:12px;color:#8B7B5C;">Teléfono</label><input type="tel" name="phone" value="{{ $addr['phone'] ?? '' }}" style="width:100%;padding:10px;border:1px solid #E5DCC9;border-radius:8px;"></div>
                        <div style="grid-column:1/-1;"><label style="font-size:12px;color:#8B7B5C;">Dirección</label><input type="text" name="address" value="{{ $addr['address'] ?? '' }}" required style="width:100%;padding:10px;border:1px solid #E5DCC9;border-radius:8px;"></div>
                        <div><label style="font-size:12px;color:#8B7B5C;">Ciudad</label><input type="text" name="city" value="{{ $addr['city'] ?? '' }}" required style="width:100%;padding:10px;border:1px solid #E5DCC9;border-radius:8px;"></div>
                        <div><label style="font-size:12px;color:#8B7B5C;">Departamento</label><input type="text" name="state" value="{{ $addr['state'] ?? '' }}" required style="width:100%;padding:10px;border:1px solid #E5DCC9;border-radius:8px;"></div>
                        <div><label style="font-size:12px;color:#8B7B5C;">Código postal</label><input type="text" name="zip_code" value="{{ $addr['zip_code'] ?? '' }}" style="width:100%;padding:10px;border:1px solid #E5DCC9;border-radius:8px;"></div>
                    </div>
                    <div style="margin-top:14px;text-align:right;">
                        <button type="submit" style="padding:10px 18px;border-radius:999px;background:#2E2A26;color:#FBF8F2;border:none;font-size:13px;font-weight:600;cursor:pointer;">Guardar dirección</button>
                    </div>
                </form>
            </section>

            <section style="background:#fff;border:1px solid #E5DCC9;border-radius:16px;padding:22px 24px;">
                <h2 style="font-family:'Playfair Display',serif;font-size:20px;color:#2E2A26;margin:0 0 12px;">Historial de entregas</h2>
                @if($subscription->deliveries->isEmpty())
                    <p style="color:#8B7B5C;margin:0;">Aún no hay entregas — tu primera llegará el {{ $subscription->next_delivery_at?->translatedFormat('l j \d\e F') ?? 'próximamente' }}.</p>
                @else
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="text-align:left;color:#8B7B5C;font-size:12px;text-transform:uppercase;letter-spacing:.1em;">
                                <th style="padding:8px 4px;">Fecha</th><th style="padding:8px 4px;">Pedido</th><th style="padding:8px 4px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($subscription->deliveries()->latest('scheduled_at')->get() as $d)
                            @php $ds = $delStatusMap[$d->status] ?? [$d->status,'#6B6157','#EFE7D8']; @endphp
                            <tr style="border-top:1px solid #EFE7D8;">
                                <td style="padding:10px 4px;color:#3B310F;">{{ optional($d->generated_at ?? $d->scheduled_at)->translatedFormat('j M Y') }}</td>
                                <td style="padding:10px 4px;">
                                    @if($d->order)
                                        <a href="{{ route('account.order', $d->order) }}" style="color:#8A6E2E;">#{{ $d->order->id }}</a>
                                    @else — @endif
                                </td>
                                <td style="padding:10px 4px;"><span style="padding:3px 10px;border-radius:9999px;font-size:11px;font-weight:600;color:{{ $ds[1] }};background:{{ $ds[2] }};">{{ $ds[0] }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </section>
        </div>
    </div>
</section>
@endsection
