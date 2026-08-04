@extends('layouts.app')

@section('title', 'Mi cuenta | Belleza Áurea')
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
    $pill = function ($map, $key) {
        $p = $map[$key] ?? [ucfirst((string) $key), '#6B6157', '#EFE7D8'];
        return "display:inline-block;padding:4px 12px;border-radius:9999px;font-size:12px;font-weight:600;color:{$p[1]};background:{$p[2]};";
    };
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

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3.5vw,32px);font-weight:700;color:#2E2A26;margin:0;">
                Hola, {{ $customer->name }} 👋
            </h1>
            <p style="margin:8px 0 0;color:#6B6157;font-size:15px;">Tienes {{ $ordersCount }} pedido(s)</p>

            <div style="margin-top:26px;background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(20px,3vw,28px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
                    <h2 style="font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:#2E2A26;margin:0;">Pedidos recientes</h2>
                    @if($recentOrders->isNotEmpty())
                        <a href="{{ route('account.orders') }}" style="font-size:13px;font-weight:600;color:#BE9A53;text-decoration:none;">Ver todos mis pedidos →</a>
                    @endif
                </div>

                @if($recentOrders->isEmpty())
                    <div style="text-align:center;padding:34px 16px;">
                        <p style="font-size:40px;margin:0;">🛍️</p>
                        <p style="margin:12px 0 0;color:#6B6157;font-size:15px;">Aún no tienes pedidos.</p>
                        <a href="{{ route('products.index') }}"
                           style="display:inline-block;margin-top:16px;padding:11px 24px;border-radius:9999px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);text-decoration:none;">
                            Explorar productos
                        </a>
                    </div>
                @else
                    <div style="display:flex;flex-direction:column;gap:10px;margin-top:14px;">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('account.order', $order) }}"
                               style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;padding:14px 16px;border:1px solid #EFE7D8;border-radius:14px;text-decoration:none;transition:border-color .2s,background .2s;"
                               onmouseover="this.style.borderColor='#D9B56D';this.style.background='#FCFAF5'"
                               onmouseout="this.style.borderColor='#EFE7D8';this.style.background='transparent'">
                                <div style="min-width:0;">
                                    <p style="margin:0;font-weight:700;color:#2E2A26;font-size:15px;">Pedido #{{ $order->id }}</p>
                                    <p style="margin:3px 0 0;color:#6B6157;font-size:13px;">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                                <span style="{{ $pill($statusMap, $order->status) }}">{{ ($statusMap[$order->status][0] ?? ucfirst((string) $order->status)) }}</span>
                                <span style="font-weight:700;color:#2E2A26;font-size:15px;white-space:nowrap;">${{ number_format($order->total, 0, ',', '.') }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
