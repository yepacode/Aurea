@extends('layouts.email')

@section('title', "Pedido #{$order->id} — {$statusLabel}")

@section('content')
    {{-- Icon --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:0 0 24px;">
                <div style="width:64px;height:64px;border-radius:50%;display:inline-block;line-height:64px;text-align:center;
                    {{ match($order->status) {
                        'confirmed' => 'background-color:#EFF6FF;',
                        'shipped' => 'background-color:#EFF6FF;',
                        'delivered' => 'background-color:#F0FDF4;',
                        'cancelled' => 'background-color:#FEF2F2;',
                        default => 'background-color:#FEF9C3;',
                    } }}">
                    <span style="font-size:32px;line-height:64px;">{{ match($order->status) {
                        'confirmed' => '✓',
                        'shipped' => '📦',
                        'delivered' => '🎉',
                        'cancelled' => '✕',
                        default => '⏳',
                    } }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Heading --}}
    <h1 style="margin:0 0 8px;font-size:26px;font-weight:700;color:#1A1A2E;text-align:center;">
        {{ match($order->status) {
            'confirmed' => '¡Pedido confirmado!',
            'shipped' => '¡Tu pedido va en camino!',
            'delivered' => '¡Pedido entregado!',
            'cancelled' => 'Pedido cancelado',
            default => 'Actualización de tu pedido',
        } }}
    </h1>
    <p style="margin:0 0 32px;font-size:15px;color:#4B5563;line-height:1.6;text-align:center;">
        Hola <strong>{{ $order->customer->name }}</strong>, tu pedido <strong style="color:#2E2A26;">#{{ $order->id }}</strong>
        {{ match($order->status) {
            'confirmed' => 'ha sido confirmado y está siendo preparado.',
            'shipped' => 'ha sido enviado.',
            'delivered' => 'ha sido entregado exitosamente.',
            'cancelled' => 'ha sido cancelado.',
            default => 'ha sido actualizado.',
        } }}
    </p>

    {{-- Status badge --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td align="center">
                <span style="display:inline-block;padding:8px 24px;border-radius:20px;font-size:14px;font-weight:700;
                    {{ match($order->status) {
                        'confirmed' => 'background-color:#EFF6FF;color:#1E40AF;',
                        'shipped' => 'background-color:#F5F3FF;color:#6D28D9;',
                        'delivered' => 'background-color:#F0FDF4;color:#166534;',
                        'cancelled' => 'background-color:#FEF2F2;color:#991B1B;',
                        default => 'background-color:#FEF9C3;color:#854D0E;',
                    } }}">
                    {{ $statusLabel }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Tracking info if shipped --}}
    @if($order->status === 'shipped' && $order->tracking_number)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FBF8F2;border:1px solid #E8CC92;border-radius:12px;margin-bottom:28px;">
        <tr>
            <td style="padding:20px;">
                @if($order->shipping_carrier)
                <p style="margin:0 0 4px;font-size:12px;font-weight:700;color:#2E2A26;text-transform:uppercase;">Paquetería</p>
                <p style="margin:0 0 12px;font-size:15px;font-weight:600;color:#1A1A2E;">{{ $order->shipping_carrier }}</p>
                @endif
                <p style="margin:0 0 4px;font-size:12px;font-weight:700;color:#2E2A26;text-transform:uppercase;">Número de guía</p>
                <p style="margin:0;font-size:17px;font-weight:700;color:#2E2A26;letter-spacing:1px;font-family:'Courier New',monospace;">{{ $order->tracking_number }}</p>
                @if($order->tracking_url)
                <p style="margin:12px 0 0;">
                    <a href="{{ $order->tracking_url }}" style="display:inline-block;background-color:#2E2A26;color:#FFFFFF;font-size:13px;font-weight:600;text-decoration:none;padding:10px 24px;border-radius:8px;">
                        Rastrear envío →
                    </a>
                </p>
                @endif
            </td>
        </tr>
    </table>
    @endif

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

    {{-- CTA --}}
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
                <p style="margin:0 0 4px;font-size:13px;color:#9CA3AF;">¿Tienes dudas sobre tu pedido?</p>
                <p style="margin:0;font-size:13px;">
                    <a href="mailto:contacto@bellezaaurea.com" style="color:#D9B56D;text-decoration:none;font-weight:600;">contacto@bellezaaurea.com</a>
                </p>
            </td>
        </tr>
    </table>
@endsection
