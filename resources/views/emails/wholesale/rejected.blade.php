@extends('layouts.email')

@section('title', 'Sobre tu solicitud de mayorista — Belleza Áurea')

@section('content')
    <h1 style="margin:0 0 8px;font-family:Georgia,'Times New Roman',serif;font-size:24px;font-weight:600;color:#2E2A26;">
        Sobre tu solicitud de mayorista
    </h1>
    <p style="margin:0 0 20px;color:#6B6157;font-size:15px;line-height:1.55;">
        Hola {{ $customer->name }}, gracias por tu interés en el programa de distribuidoras de Belleza Áurea.
        Después de revisar tu solicitud, por ahora no podemos aprobarla.
    </p>

    @if($customer->wholesaler_notes)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="background:#F8E9E6;border:1px solid #E5C3BB;border-radius:12px;margin-bottom:20px;">
            <tr>
                <td style="padding:18px 22px;">
                    <p style="margin:0 0 6px;font-size:12px;letter-spacing:0.14em;text-transform:uppercase;font-weight:700;color:#8A4237;">
                        Motivo
                    </p>
                    <p style="margin:0;color:#2E2A26;font-size:14px;line-height:1.6;white-space:pre-wrap;">
                        {{ $customer->wholesaler_notes }}
                    </p>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:20px 0;color:#6B6157;font-size:14px;line-height:1.6;">
        Puedes volver a solicitar acceso en cualquier momento desde tu cuenta si tu situación cambia
        o si quieres compartir información adicional.
    </p>

    <p style="text-align:center;margin:24px 0;">
        <a href="{{ route('account.wholesale.request') }}"
           style="display:inline-block;padding:13px 28px;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);color:#3B310F;font-weight:700;font-size:14px;text-decoration:none;border-radius:9999px;">
            Volver a solicitar →
        </a>
    </p>

    <p style="margin:24px 0 0;color:#6B6157;font-size:14px;">
        Si tienes dudas, respóndenos este correo.<br>
        <strong style="color:#2E2A26;">— El equipo de Belleza Áurea</strong>
    </p>
@endsection
