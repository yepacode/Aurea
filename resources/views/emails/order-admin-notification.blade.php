@extends('layouts.email')

@section('title', "Nuevo pedido #{$order->id}")

@section('content')
    {{-- Icon --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:0 0 24px;">
                <div style="width:64px;height:64px;border-radius:50%;background-color:#EFF6FF;display:inline-block;line-height:64px;text-align:center;">
                    <span style="font-size:32px;line-height:64px;">&#128230;</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Heading --}}
    <h1 style="margin:0 0 8px;font-size:26px;font-weight:700;color:#1A1A2E;text-align:center;">
        Nuevo pedido recibido
    </h1>
    <p style="margin:0 0 32px;font-size:15px;color:#4B5563;line-height:1.6;text-align:center;">
        Se ha confirmado el pedido <strong style="color:#2E2A26;">#{{ $order->id }}</strong> de <strong>{{ $order->customer->name }}</strong>.
    </p>

    {{-- Order details card --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
        <tr>
            <td style="padding:12px 16px;background-color:#2E2A26;border-radius:8px 8px 0 0;font-size:13px;font-weight:700;color:#FFFFFF;text-transform:uppercase;letter-spacing:0.5px;">
                Datos del pedido
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E5E7EB;border-top:none;border-radius:0 0 8px 8px;overflow:hidden;margin-bottom:24px;">
        <tr>
            <td style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#6B7280;">Cliente</td>
            <td align="right" style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;font-weight:600;">{{ $order->customer->name }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#6B7280;">Correo</td>
            <td align="right" style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;">{{ $order->customer->email }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#6B7280;">Teléfono</td>
            <td align="right" style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;">{{ $order->customer->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#6B7280;">Método de pago</td>
            <td align="right" style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#1A1A2E;font-weight:600;">
                @switch($order->payment_method)
                    @case('card') Tarjeta @break
                    @case('transfer') Transferencia @break
                    @case('cash_on_delivery') Contra entrega @break
                    @default {{ $order->payment_method }}
                @endswitch
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;color:#6B7280;">Estado de pago</td>
            <td align="right" style="padding:12px 16px;border-bottom:1px solid #F3F4F6;font-size:14px;font-weight:600;color:{{ $order->payment_status === 'paid' ? '#059669' : '#D97706' }};">
                {{ $order->payment_status === 'paid' ? 'Pagado' : 'Pendiente' }}
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px;font-size:14px;color:#6B7280;">Dirección de envío</td>
            <td align="right" style="padding:12px 16px;font-size:14px;color:#1A1A2E;">{{ $order->shipping_address }}</td>
        </tr>
    </table>

    {{-- Items --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
        <tr>
            <td style="padding:12px 16px;background-color:#2E2A26;border-radius:8px 8px 0 0;font-size:13px;font-weight:700;color:#FFFFFF;text-transform:uppercase;letter-spacing:0.5px;">
                Productos
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
                        <td align="right" style="padding:8px 0 0;font-size:20px;font-weight:700;color:#2E2A26;">${{ number_format($order->total, 0, ',', '.') }} COP</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Notes --}}
    @if($order->notes)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td style="padding:14px 16px;background-color:#FEF3C7;border-radius:8px;font-size:14px;color:#92400E;">
                <strong>Notas del cliente:</strong> {{ $order->notes }}
            </td>
        </tr>
    </table>
    @endif

    {{-- CTA: View in admin --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td align="center">
                <a href="{{ route('admin.orders.show', $order) }}"
                   style="display:inline-block;background-color:#2E2A26;color:#FFFFFF;font-size:16px;font-weight:700;text-decoration:none;padding:16px 40px;border-radius:8px;letter-spacing:0.3px;">
                    Ver pedido en admin
                </a>
            </td>
        </tr>
    </table>
@endsection
