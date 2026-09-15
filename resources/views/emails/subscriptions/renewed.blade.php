@extends('layouts.email')

@section('title', 'Tu ritual Áurea va en camino')

@section('content')
    <h1 style="margin:0 0 12px;font-family:'Georgia',serif;font-size:26px;color:#2E2A26;">📦 Tu ritual va en camino</h1>
    <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.6;">
        Hola <strong>{{ $subscription->customer->name }}</strong>, ya preparamos tu entrega mensual de
        <strong style="color:#2E2A26;">{{ $subscription->plan->name }}</strong>.
    </p>
    <p style="margin:0 0 24px;font-size:15px;color:#4B5563;line-height:1.6;">
        Pedido <strong style="color:#D9B56D;">#{{ $order->id }}</strong> —
        Total <strong>${{ number_format((float) $order->total, 0, ',', '.') }}</strong>
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FBF8F2;border-radius:8px;">
        <tr><td style="padding:20px 24px;">
            <p style="margin:0 0 10px;font-size:13px;color:#8B7B5C;text-transform:uppercase;letter-spacing:1px;">Este mes recibes</p>
            @foreach($order->items as $item)
                <p style="margin:6px 0;font-size:14px;color:#2E2A26;">
                    ✓ {{ optional($item->product)->name ?? 'Producto' }} <span style="color:#8B7B5C;">× {{ $item->qty }}</span>
                </p>
            @endforeach
        </td></tr>
    </table>

    <p style="margin:24px 0 8px;font-size:14px;color:#4B5563;">
        Sigue el envío en
        <a href="{{ route('order.track', $order->tracking_token) }}" style="color:#D9B56D;">este link de seguimiento</a>.
    </p>
    <p style="margin:0 0 8px;font-size:14px;color:#4B5563;">
        Próxima entrega:
        <strong>{{ $subscription->next_delivery_at->translatedFormat('l j \d\e F') }}</strong>
    </p>
    <p style="margin:0;font-size:14px;color:#4B5563;">Con cariño, el equipo de Belleza Áurea 💛</p>
@endsection
