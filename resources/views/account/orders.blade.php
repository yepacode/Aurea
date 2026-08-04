@extends('layouts.app')

@section('title', 'Mis pedidos | Belleza Áurea')
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
        return "display:inline-block;padding:4px 12px;border-radius:9999px;font-size:12px;font-weight:600;color:{$p[1]};background:{$p[2]};";
    };
    $label = function ($map, $key) { return $map[$key][0] ?? ucfirst((string) $key); };
@endphp

<section style="padding:clamp(32px,5vw,60px) 20px;background:#FBF8F2;min-height:60vh;">
    <div style="max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap;gap:26px;align-items:flex-start;">

        {{-- Sidebar --}}
        <div style="flex:1 1 220px;min-width:220px;max-width:280px;">
            @include('account._nav')
        </div>

        {{-- Main --}}
        <div style="flex:3 1 480px;min-width:0;">

            @if(session('success'))
                <div style="background:#EAF3EA;border:1px solid #C5E0C5;color:#3B7A3B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('success') }}</div>
            @endif

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3.5vw,32px);font-weight:700;color:#2E2A26;margin:0 0 22px;">Mis pedidos</h1>

            @if($orders->isEmpty())
                <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:44px 20px;text-align:center;">
                    <p style="font-size:40px;margin:0;">🛍️</p>
                    <p style="margin:12px 0 0;color:#6B6157;font-size:15px;">Todavía no has hecho ningún pedido.</p>
                    <a href="{{ route('products.index') }}"
                       style="display:inline-block;margin-top:16px;padding:11px 24px;border-radius:9999px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);text-decoration:none;">
                        Explorar productos
                    </a>
                </div>
            @else
                {{-- Cards (mobile-friendly), each row uses a grid --}}
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($orders as $order)
                        <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:16px;padding:18px 20px;box-shadow:0 14px 36px -32px rgba(120,92,44,.4);">
                            <div style="display:flex;flex-wrap:wrap;gap:14px 20px;align-items:center;justify-content:space-between;">
                                <div style="min-width:0;">
                                    <p style="margin:0;font-weight:700;color:#2E2A26;font-size:16px;">Pedido #{{ $order->id }}</p>
                                    <p style="margin:3px 0 0;color:#6B6157;font-size:13px;">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                                    <span style="{{ $pill($statusMap, $order->status) }}">{{ $label($statusMap, $order->status) }}</span>
                                    <span style="{{ $pill($payMap, $order->payment_status) }}">{{ $label($payMap, $order->payment_status) }}</span>
                                </div>
                                <div style="font-weight:700;color:#2E2A26;font-size:16px;white-space:nowrap;">${{ number_format($order->total, 0, ',', '.') }}</div>
                                <a href="{{ route('account.order', $order) }}"
                                   style="font-size:13px;font-weight:600;color:#BE9A53;text-decoration:none;white-space:nowrap;">Ver detalle →</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top:24px;">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
