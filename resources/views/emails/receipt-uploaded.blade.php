<x-mail::message>
# 📎 Comprobante recibido

El cliente **{{ optional($order->customer)->name ?? 'invitado' }}** subió el comprobante de transferencia para el pedido **#{{ $order->id }}**.

- **Total:** ${{ number_format($order->total, 0, ',', '.') }} COP
- **Método:** Transferencia bancaria
- **Cliente:** {{ optional($order->customer)->email ?? 's/email' }}

@if($receiptUrl)
<x-mail::button :url="$receiptUrl" color="primary">
Ver comprobante
</x-mail::button>
@endif

<x-mail::button :url="$adminOrderUrl" color="secondary">
Abrir pedido en el panel
</x-mail::button>

Revisa el comprobante y aprueba o rechaza el pago desde el panel.

Belleza Áurea
</x-mail::message>
