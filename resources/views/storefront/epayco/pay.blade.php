<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago seguro — Belleza Áurea</title>
    <style>
        *{box-sizing:border-box;margin:0;}
        body{
            font-family:'Segoe UI',system-ui,sans-serif;min-height:100vh;
            display:flex;align-items:center;justify-content:center;padding:24px;
            background:radial-gradient(120% 90% at 70% 10%,#FFFFFF 0%,#F7F2E9 55%,#EFE7D8 100%);color:#2E2A26;
        }
        .box{max-width:440px;text-align:center;}
        .brand{font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:#BE9A53;font-weight:600;margin-bottom:18px;}
        h1{font-family:Georgia,serif;font-weight:600;font-size:26px;margin-bottom:10px;}
        p{color:#6B6157;font-size:14.5px;line-height:1.6;margin-bottom:26px;}
        .amount{font-family:Georgia,serif;font-size:34px;color:#2E2A26;margin:6px 0 4px;}
        .amount small{font-size:14px;color:#9CA3AF;}
        .spinner{width:42px;height:42px;border:3px solid #E8DFCE;border-top-color:#D9B56D;border-radius:50%;
            animation:spin .8s linear infinite;margin:0 auto 22px;}
        @keyframes spin{to{transform:rotate(360deg);}}
        .btn{display:inline-flex;align-items:center;gap:9px;border:none;cursor:pointer;text-decoration:none;
            border-radius:999px;padding:15px 32px;color:#3B310F;font:600 12.5px/1 'Segoe UI',sans-serif;
            letter-spacing:.1em;text-transform:uppercase;
            background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);box-shadow:0 16px 32px -12px rgba(190,154,83,.7);}
        .btn:hover{transform:translateY(-2px);}
        .ghost{display:inline-block;margin-top:16px;color:#9CA3AF;font-size:12.5px;text-decoration:underline;}
        @media (prefers-reduced-motion:reduce){.spinner{animation:none;}.btn:hover{transform:none;}}
    </style>
</head>
<body>
    <div class="box">
        <div class="brand">Belleza Áurea</div>
        <div class="spinner" id="spin"></div>
        <h1>Abriendo el pago seguro…</h1>
        <div class="amount">${{ number_format($order->total, 0, ',', '.') }} <small>COP</small></div>
        <p>Serás atendido por <strong>ePayco</strong> (PSE, tarjetas, Nequi y efectivo) para completar el pago de tu pedido. Si la ventana no abre sola, toca el botón.</p>
        <button class="btn" id="payBtn" type="button">Pagar ahora</button>
        <br>
        <a class="ghost" href="{{ route('checkout.index') }}">Volver al checkout</a>
    </div>

    <script src="https://checkout.epayco.co/checkout.js"></script>
    <script>
        function openEpayco(){
            if (typeof ePayco === 'undefined') {
                alert('No se pudo cargar el pago. Revisa tu conexión e intenta de nuevo.');
                return;
            }
            var handler = ePayco.checkout.configure({
                key: @js($publicKey),
                test: {{ $test ? 'true' : 'false' }}
            });
            handler.open({
                name: 'Belleza Áurea',
                description: 'Pedido #{{ $order->id }}',
                invoice: @js((string) $order->id),
                currency: @js($currency),
                amount: @js((string) (int) round($order->total)),
                tax_base: '0',
                tax: '0',
                country: 'co',
                lang: 'es',
                external: 'false',
                response: @js(route('epayco.response')),
                confirmation: @js(route('epayco.confirmation')),
                name_billing: @js($order->customer->name ?? ''),
                email_billing: @js($order->customer->email ?? ''),
                mobilephone_billing: @js($order->customer->phone ?? ''),
                extra1: @js((string) $order->id)
            });
        }
        document.getElementById('payBtn').addEventListener('click', openEpayco);
        // Auto-abrir cuando el script haya cargado.
        window.addEventListener('load', function(){ setTimeout(openEpayco, 600); });
    </script>
</body>
</html>
