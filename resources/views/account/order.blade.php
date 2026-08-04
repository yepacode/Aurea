@extends('layouts.app')

@section('title', 'Pedido #'.$order->id.' | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $statusMap = [
        'pending'   => ['Pendiente',  '#8A6D1F', '#FBF0D5'],
        'confirmed' => ['Confirmada', '#3B7A3B', '#EAF3EA'],
        'shipped'   => ['Enviada',    '#2F6F9E', '#E4EEF6'],
        'delivered' => ['Entregada',  '#3B7A3B', '#EAF3EA'],
        'cancelled' => ['Cancelada',  '#C0554A', '#F8E9E6'],
    ];
    $payMap = [
        'paid'       => ['Pagado',     '#3B7A3B', '#EAF3EA'],
        'pending'    => ['Pendiente',  '#8A6D1F', '#FBF0D5'],
        'processing' => ['En proceso', '#8A6D1F', '#FBF0D5'],
        'failed'     => ['Rechazado',  '#C0554A', '#F8E9E6'],
        'refunded'   => ['Reembolsado','#6B6157', '#EFE7D8'],
    ];
    $pill = function ($map, $key) {
        $p = $map[$key] ?? [ucfirst((string) $key), '#6B6157', '#EFE7D8'];
        return "display:inline-block;padding:5px 14px;border-radius:9999px;font-size:12px;font-weight:600;color:{$p[1]};background:{$p[2]};";
    };
    $label = function ($map, $key) { return $map[$key][0] ?? ucfirst((string) $key); };
    $money = fn ($n) => '$' . number_format((float) $n, 0, ',', '.');
    $payMethods = ['card' => 'Tarjeta', 'transfer' => 'Transferencia', 'cash_on_delivery' => 'Contra entrega', 'epayco' => 'ePayco'];
@endphp

<section style="padding:clamp(32px,5vw,60px) 20px;background:#FBF8F2;min-height:60vh;">
    <div style="max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap;gap:26px;align-items:flex-start;">

        {{-- Sidebar --}}
        <div style="flex:1 1 220px;min-width:220px;max-width:280px;">
            @include('account._nav')
        </div>

        {{-- Main --}}
        <div style="flex:3 1 480px;min-width:0;">

            <a href="{{ route('account.orders') }}" style="display:inline-block;margin-bottom:16px;color:#BE9A53;font-size:14px;font-weight:600;text-decoration:none;">← Volver a mis pedidos</a>

            <div style="display:flex;flex-wrap:wrap;gap:12px 18px;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3.5vw,32px);font-weight:700;color:#2E2A26;margin:0;">Pedido #{{ $order->id }}</h1>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <span style="{{ $pill($statusMap, $order->status) }}">{{ $label($statusMap, $order->status) }}</span>
                    <span style="{{ $pill($payMap, $order->payment_status) }}">{{ $label($payMap, $order->payment_status) }}</span>
                </div>
            </div>
            <p style="margin:0 0 22px;color:#6B6157;font-size:14px;">Realizado el {{ $order->created_at->format('d/m/Y') }} a las {{ $order->created_at->format('H:i') }}</p>

            <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:flex-start;">

                {{-- Items + totals --}}
                <div style="flex:2 1 380px;min-width:0;background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;overflow:hidden;box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                    <div style="padding:16px 22px;border-bottom:1px solid #EFE7D8;">
                        <h2 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:#2E2A26;margin:0;">Productos</h2>
                    </div>

                    <div>
                        @foreach($order->items as $item)
                            @php
                                $variantLabel = null;
                                if ($item->variant) {
                                    $variantLabel = $item->variant->value ?? $item->variant->color ?? ($item->variant->name ?? null);
                                }
                            @endphp
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;padding:16px 22px;border-bottom:1px solid #F3ECDF;">
                                <div style="min-width:0;">
                                    <p style="margin:0;font-weight:600;color:#2E2A26;font-size:15px;overflow-wrap:anywhere;">{{ optional($item->product)->name ?? 'Producto' }}</p>
                                    @if($variantLabel)
                                        <p style="margin:3px 0 0;color:#6B6157;font-size:13px;">{{ $variantLabel }}</p>
                                    @endif
                                    <p style="margin:3px 0 0;color:#6B6157;font-size:13px;">Cant: {{ $item->qty }} × {{ $money($item->unit_price) }}</p>
                                </div>
                                <p style="margin:0;font-weight:700;color:#2E2A26;font-size:15px;white-space:nowrap;">{{ $money($item->total) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div style="padding:18px 22px;background:#FCFAF5;display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:14px;color:#6B6157;">
                            <span>Subtotal</span>
                            <span>{{ $money($order->subtotal) }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;color:#6B6157;">
                            <span>Envío</span>
                            <span>{{ $order->shipping > 0 ? $money($order->shipping) : 'Gratis' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:700;color:#2E2A26;padding-top:10px;border-top:1px solid #EFE7D8;">
                            <span>Total</span>
                            <span style="color:#BE9A53;">{{ $money($order->total) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Info sidebar --}}
                <div style="flex:1 1 240px;min-width:0;display:flex;flex-direction:column;gap:16px;">

                    <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:20px;">
                        <h3 style="font-family:'Playfair Display',serif;font-size:16px;font-weight:600;color:#2E2A26;margin:0 0 10px;">Pago</h3>
                        <div style="display:flex;justify-content:space-between;gap:12px;font-size:14px;margin-bottom:6px;">
                            <span style="color:#6B6157;">Método</span>
                            <span style="color:#2E2A26;font-weight:600;text-align:right;">{{ $payMethods[$order->payment_method] ?? $order->payment_method }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;gap:12px;font-size:14px;">
                            <span style="color:#6B6157;">Estado</span>
                            <span style="{{ $pill($payMap, $order->payment_status) }}">{{ $label($payMap, $order->payment_status) }}</span>
                        </div>
                    </div>

                    <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:20px;">
                        <h3 style="font-family:'Playfair Display',serif;font-size:16px;font-weight:600;color:#2E2A26;margin:0 0 10px;">Dirección de envío</h3>
                        <p style="margin:0;color:#6B6157;font-size:14px;line-height:1.6;overflow-wrap:anywhere;">{{ $order->shipping_address ?: 'No especificada' }}</p>
                    </div>

                    @if($order->tracking_token)
                        <a href="{{ route('order.track', $order->tracking_token) }}"
                           style="display:block;text-align:center;padding:12px 20px;border-radius:9999px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);text-decoration:none;transition:filter .2s;"
                           onmouseover="this.style.filter='brightness(1.04)'" onmouseout="this.style.filter='none'">
                            Seguir mi pedido
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
