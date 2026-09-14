@extends('layouts.email')

@section('title', 'Bienvenida al programa mayorista — Belleza Áurea')

@section('content')
    <h1 style="margin:0 0 8px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#2E2A26;">
        ¡Bienvenida, {{ $customer->name }}! 💛
    </h1>
    <p style="margin:0 0 22px;color:#6B6157;font-size:15px;line-height:1.55;">
        Tu solicitud fue aprobada. A partir de ahora <strong>{{ $customer->wholesaler_company_name }}</strong>
        forma parte del programa de distribuidoras de Belleza Áurea.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:linear-gradient(135deg,#FFF8E8,#F6E6C0 55%,#EBCF90);border:1px solid #E0BE77;border-radius:14px;margin-bottom:22px;">
        <tr>
            <td style="padding:22px 24px;">
                <p style="margin:0 0 6px;font-size:12px;letter-spacing:0.14em;text-transform:uppercase;font-weight:700;color:#8A6E2E;">
                    Tus beneficios
                </p>
                <ul style="margin:0;padding:0 0 0 18px;color:#3B310F;font-size:14px;line-height:1.7;">
                    <li>Precios preferenciales aplicados automáticamente en todo el catálogo.</li>
                    <li>Envío preferencial en tus pedidos.</li>
                    <li>Atención directa por WhatsApp para tus reposiciones.</li>
                    <li>Acceso a lanzamientos y colecciones exclusivas.</li>
                </ul>
            </td>
        </tr>
    </table>

    <p style="text-align:center;margin:24px 0;">
        <a href="{{ route('products.index') }}"
           style="display:inline-block;padding:14px 32px;background:#2E2A26;color:#F7F3ED;font-weight:700;font-size:14px;text-decoration:none;border-radius:9999px;">
            Explorar catálogo con precios mayoristas →
        </a>
    </p>

    <p style="margin:24px 0 0;color:#6B6157;font-size:14px;line-height:1.55;">
        En tu cuenta verás una insignia dorada de <strong>Mayorista aprobada</strong>
        y los precios se actualizan sin que tengas que hacer nada más.
    </p>

    <p style="margin:16px 0 0;color:#6B6157;font-size:14px;">
        Cualquier duda, respóndenos este correo.<br>
        <strong style="color:#2E2A26;">— El equipo de Belleza Áurea</strong>
    </p>
@endsection
