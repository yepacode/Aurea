@extends('layouts.email')

@section('title', "Pedido #{$order->id} confirmado")

@section('content')
    {{-- Success icon --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:0 0 24px;">
                <div style="width:64px;height:64px;border-radius:50%;background-color:#ECFDF5;display:inline-block;line-height:64px;text-align:center;">
                    <span style="font-size:32px;line-height:64px;">&#10003;</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Heading --}}
    <h1 style="margin:0 0 8px;font-size:26px;font-weight:700;color:#1A1A2E;text-align:center;">
        ¡Te compartimos los datos para realizar la transferencia !
    </h1>
    <p style="margin:0 0 8px;font-size:15px;color:#4B5563;line-height:1.6;text-align:center;">
        Hola <strong>{{ $order->customer->name }}</strong>, tu pedido <strong style="color:#2E2A26;">#{{ $order->id }}</strong> ha sido recibido.
    </p>
    <p style="margin:0 0 32px;font-size:14px;color:#9CA3AF;text-align:center;">
        {{ $order->created_at->format('d/m/Y') }} &middot; {{ $order->created_at->format('H:i') }} hrs
    </p>

    {{-- Order status badge --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td align="center">
                <span style="display:inline-block;background-color:#ECFDF5;color:#059669;font-size:13px;font-weight:700;padding:8px 20px;border-radius:20px;text-transform:uppercase;letter-spacing:0.5px;">
                    @if($order->payment_status === 'paid')
                        &#9679; Pago exitoso
                    @else
                        &#9679; Pedido recibido
                    @endif
                </span>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
        <tr>
            <td style="padding:12px 16px;background-color:#2E2A26;border-radius:8px 8px 0 0;font-size:13px;font-weight:700;color:#FFFFFF;text-transform:uppercase;letter-spacing:0.5px;">
                Resumen del pedido
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E5E7EB;border-top:none;border-radius:0 0 8px 8px;overflow:hidden;margin-bottom:24px;">
        @foreach($order->items as $item)
        <tr>
            <td style="padding:14px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;">
                <strong>{{ $item->product->name ?? 'Producto' }}</strong>
                @if($item->variant)
                    <br><span style="font-size:12px;color:#6B7280;">{{ $item->variant->name ?? $item->variant->value ?? '' }}</span>
                @endif
            </td>
            <td align="center" style="padding:14px 8px;border-bottom:1px solid #F3F4F6;font-size:13px;color:#6B7280;" width="50">
                x{{ $item->qty }}
            </td>
            <td align="right" style="padding:14px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;font-weight:600;" width="100">
                ${{ number_format($item->total, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </table>

    {{-- Totals --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F9FAFB;border-radius:8px;margin-bottom:28px;">
        <tr>
            <td style="padding:16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding:4px 0;font-size:14px;color:#6B7280;">Subtotal</td>
                        <td align="right" style="padding:4px 0;font-size:14px;color:#6B7280;">${{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($order->discount_2x1 > 0)
                    <tr>
                        <td style="padding:4px 0;font-size:14px;color:#059669;">Descuento 2×1</td>
                        <td align="right" style="padding:4px 0;font-size:14px;color:#059669;">-${{ number_format($order->discount_2x1, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($order->discount_coupon > 0)
                    <tr>
                        <td style="padding:4px 0;font-size:14px;color:#059669;">Cupón{{ $order->discount_code ? " ({$order->discount_code})" : '' }}</td>
                        <td align="right" style="padding:4px 0;font-size:14px;color:#059669;">-${{ number_format($order->discount_coupon, 0, ',', '.') }}</td>
                    </tr>
                    @elseif($order->discount_amount > 0 && $order->discount_2x1 == 0)
                    <tr>
                        <td style="padding:4px 0;font-size:14px;color:#059669;">Descuento {{ $order->discount_code ? "({$order->discount_code})" : '' }}</td>
                        <td align="right" style="padding:4px 0;font-size:14px;color:#059669;">-${{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:4px 0;font-size:14px;color:#6B7280;">Envío</td>
                        <td align="right" style="padding:4px 0;font-size:14px;color:#6B7280;">
                            @if($order->shipping > 0)
                                ${{ number_format($order->shipping, 0, ',', '.') }}
                            @else
                                <span style="color:#059669;">Gratis</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:8px 0 0;"><hr style="border:none;border-top:1px solid #E5E7EB;margin:0;"></td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0 0;font-size:20px;font-weight:700;color:#2E2A26;">Total</td>
                        <td align="right" style="padding:8px 0 0;font-size:20px;font-weight:700;color:#2E2A26;">${{ number_format($order->total, 0, ',', '.') }} MXN</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Payment method --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td style="padding:14px 16px;background-color:#FBF8F2;border-radius:8px;font-size:14px;color:#4B5563;">
                <strong style="color:#2E2A26;">Método de pago:</strong>
                @switch($order->payment_method)
                    @case('card') Tarjeta de crédito/débito @break
                    @case('transfer') Transferencia bancaria @break
                    @case('cash_on_delivery') Pago contra entrega @break
                    @default {{ $order->payment_method }}
                @endswitch
            </td>
        </tr>
    </table>

    {{-- Bank transfer details --}}
    @if($order->payment_method === 'transfer' && !empty($bankDetails['clabe']))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td style="padding:0;border-radius:8px;overflow:hidden;border:1px solid #E8CC92;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="background-color:#2E2A26;padding:12px 16px;color:#FFFFFF;font-size:15px;font-weight:700;">
                            Datos para transferencia bancaria
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#FBF4E6;padding:16px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#2E2A26;">
                                @if($bankDetails['bank_name'])
                                <tr><td style="padding:4px 0;color:#6B7280;width:130px;">Banco:</td><td style="padding:4px 0;font-weight:600;">{{ $bankDetails['bank_name'] }}</td></tr>
                                @endif
                                @if($bankDetails['account_holder'])
                                <tr><td style="padding:4px 0;color:#6B7280;">Beneficiario:</td><td style="padding:4px 0;font-weight:600;">{{ $bankDetails['account_holder'] }}</td></tr>
                                @endif
                                <tr><td style="padding:4px 0;color:#6B7280;">CLABE:</td><td style="padding:4px 0;font-weight:700;font-family:monospace;letter-spacing:2px;font-size:15px;">{{ $bankDetails['clabe'] }}</td></tr>
                                @if($bankDetails['account_number'])
                                <tr><td style="padding:4px 0;color:#6B7280;">No. cuenta:</td><td style="padding:4px 0;font-weight:600;">{{ $bankDetails['account_number'] }}</td></tr>
                                @endif
                                <tr><td style="padding:4px 0;color:#6B7280;">Referencia:</td><td style="padding:4px 0;font-weight:700;color:#2E2A26;">Pedido #{{ $order->id }}</td></tr>
                                <tr><td style="padding:4px 0;color:#6B7280;">Monto:</td><td style="padding:4px 0;font-weight:700;color:#2E2A26;font-size:16px;">${{ number_format($order->total, 0, ',', '.') }} MXN</td></tr>
                            </table>
                            @if($bankDetails['reference_instructions'])
                            <p style="margin:12px 0 0;font-size:13px;color:#4B5563;">{{ $bankDetails['reference_instructions'] }}</p>
                            @endif
                            @if($bankDetails['additional_notes'])
                            <p style="margin:8px 0 0;font-size:13px;color:#6B7280;font-style:italic;">{{ $bankDetails['additional_notes'] }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @endif

    {{-- Shipping info --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
        <tr>
            <td style="padding:14px 16px;background-color:#FBF8F2;border-radius:8px;font-size:14px;color:#4B5563;">
                <strong style="color:#2E2A26;">Envío a:</strong>
                {{ $order->shipping_address }}
                @if($order->notes)
                    <br><span style="font-size:13px;color:#9CA3AF;"><strong>Notas:</strong> {{ $order->notes }}</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- CTA: Track order --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td align="center">
                <a href="{{ route('order.track', $order->tracking_token) }}"
                   style="display:inline-block;background-color:#2E2A26;color:#FFFFFF;font-size:16px;font-weight:700;text-decoration:none;padding:16px 40px;border-radius:8px;letter-spacing:0.3px;">
                    {{ $order->payment_method === 'transfer' ? 'Ya hice mi transferencia' : 'Seguir mi pedido' }}
                </a>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td align="center">
                <a href="{{ route('products.index') }}"
                   style="display:inline-block;color:#D9B56D;font-size:14px;font-weight:600;text-decoration:none;padding:8px 24px;">
                    Seguir comprando &rarr;
                </a>
            </td>
        </tr>
    </table>

    {{-- Help footer --}}
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
