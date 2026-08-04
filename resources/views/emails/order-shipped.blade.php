@extends('layouts.email')

@section('title', "Pedido #{$order->id} en camino")

@section('content')
    {{-- Shipping icon --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:0 0 24px;">
                <div style="width:64px;height:64px;border-radius:50%;background-color:#EFF6FF;display:inline-block;line-height:64px;text-align:center;">
                    <span style="font-size:32px;line-height:64px;">&#128666;</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Heading --}}
    <h1 style="margin:0 0 8px;font-size:26px;font-weight:700;color:#1A1A2E;text-align:center;">
        ¡Tu pedido va en camino!
    </h1>
    <p style="margin:0 0 32px;font-size:15px;color:#4B5563;line-height:1.6;text-align:center;">
        Hola <strong>{{ $order->customer->name }}</strong>, tu pedido <strong style="color:#2E2A26;">#{{ $order->id }}</strong> ha sido enviado.
    </p>

    {{-- Tracking info card --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FBF8F2;border:1px solid #E8CC92;border-radius:12px;margin-bottom:28px;">
        <tr>
            <td style="padding:24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    {{-- Carrier --}}
                    @if($order->shipping_carrier)
                    <tr>
                        <td style="padding:0 0 12px;">
                            <p style="margin:0 0 2px;font-size:12px;font-weight:700;color:#2E2A26;text-transform:uppercase;letter-spacing:0.5px;">Paquetería</p>
                            <p style="margin:0;font-size:16px;font-weight:600;color:#1A1A2E;">{{ $order->shipping_carrier }}</p>
                        </td>
                    </tr>
                    @endif

                    {{-- Tracking number --}}
                    @if($order->tracking_number)
                    <tr>
                        <td style="padding:0 0 12px;">
                            <p style="margin:0 0 2px;font-size:12px;font-weight:700;color:#2E2A26;text-transform:uppercase;letter-spacing:0.5px;">Número de guía</p>
                            <p style="margin:0;font-size:18px;font-weight:700;color:#2E2A26;letter-spacing:1px;font-family:'Courier New',monospace;">{{ $order->tracking_number }}</p>
                        </td>
                    </tr>
                    @endif

                    {{-- Track button --}}
                    @if($order->tracking_url)
                    <tr>
                        <td style="padding:4px 0 0;">
                            <a href="{{ $order->tracking_url }}"
                               style="display:inline-block;background-color:#2E2A26;color:#FFFFFF;font-size:14px;font-weight:600;text-decoration:none;padding:12px 28px;border-radius:8px;">
                                Rastrear envío &rarr;
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Shipping address --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td style="padding:14px 16px;background-color:#F9FAFB;border-radius:8px;font-size:14px;color:#4B5563;">
                <strong style="color:#2E2A26;">Dirección de entrega:</strong>
                {{ $order->shipping_address }}
            </td>
        </tr>
    </table>

    {{-- Order summary --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
        <tr>
            <td style="padding:10px 16px;background-color:#2E2A26;border-radius:8px 8px 0 0;font-size:13px;font-weight:700;color:#FFFFFF;text-transform:uppercase;letter-spacing:0.5px;">
                Resumen del pedido
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E5E7EB;border-top:none;border-radius:0 0 8px 8px;overflow:hidden;margin-bottom:24px;">
        @foreach($order->items as $item)
        <tr>
            <td style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;">
                {{ $item->product->name ?? 'Producto' }}
                @if($item->variant)
                    <span style="font-size:12px;color:#6B7280;"> — {{ $item->variant->name ?? $item->variant->value ?? '' }}</span>
                @endif
            </td>
            <td align="center" style="padding:12px 4px;border-bottom:1px solid #F3F4F6;font-size:13px;color:#6B7280;" width="40">
                x{{ $item->qty }}
            </td>
            <td align="right" style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;font-weight:600;" width="90">
                ${{ number_format($item->total, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" style="padding:12px 16px;font-size:15px;font-weight:700;color:#2E2A26;background-color:#F9FAFB;">Total</td>
            <td align="right" style="padding:12px 16px;font-size:15px;font-weight:700;color:#2E2A26;background-color:#F9FAFB;">${{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- CTA: Track order page --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td align="center">
                <a href="{{ route('order.track', $order->tracking_token) }}"
                   style="display:inline-block;background-color:#D9B56D;color:#FFFFFF;font-size:15px;font-weight:600;text-decoration:none;padding:14px 36px;border-radius:8px;">
                    Ver estado de mi pedido
                </a>
            </td>
        </tr>
    </table>

    {{-- Help --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="border-top:1px solid #E5E7EB;padding:20px 0 0;">
                <p style="margin:0 0 4px;font-size:13px;color:#9CA3AF;">¿Tienes dudas sobre tu envío?</p>
                <p style="margin:0;font-size:13px;">
                    <a href="mailto:contacto@bellezaaurea.com" style="color:#D9B56D;text-decoration:none;font-weight:600;">contacto@bellezaaurea.com</a>
                </p>
            </td>
        </tr>
    </table>
@endsection
