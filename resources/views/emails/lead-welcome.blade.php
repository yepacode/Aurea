@extends('layouts.email')

@section('title', 'Bienvenida a Belleza Áurea')

@section('content')
    {{-- Heading --}}
    <h1 style="margin:0 0 8px;font-size:24px;font-weight:700;color:#1A1A2E;">
        ¡Bienvenido{{ $lead->name ? ', ' . $lead->name : '' }}!
    </h1>
    <p style="margin:0 0 24px;font-size:15px;color:#4B5563;line-height:1.6;">
        Gracias por suscribirte a Belleza Áurea. Ahora recibirás rituales de belleza, tips de skincare y ofertas exclusivas.
    </p>

    {{-- Benefits --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FBF8F2;border-radius:8px;margin-bottom:32px;">
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;font-size:14px;font-weight:700;color:#2E2A26;text-transform:uppercase;letter-spacing:0.5px;">
                    ¿Sabías que...?
                </p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="24" valign="top" style="padding:4px 8px 12px 0;font-size:16px;color:#D9B56D;">&#10003;</td>
                        <td style="padding:4px 0 12px;font-size:14px;color:#4B5563;line-height:1.5;">
                            Una rutina constante transforma tu piel en <strong>pocas semanas</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="24" valign="top" style="padding:4px 8px 12px 0;font-size:16px;color:#D9B56D;">&#10003;</td>
                        <td style="padding:4px 0 12px;font-size:14px;color:#4B5563;line-height:1.5;">
                            Cuidar tu piel hoy previene <strong>signos de la edad</strong> mañana
                        </td>
                    </tr>
                    <tr>
                        <td width="24" valign="top" style="padding:4px 8px 0;font-size:16px;color:#D9B56D;">&#10003;</td>
                        <td style="padding:4px 0 0;font-size:14px;color:#4B5563;line-height:1.5;">
                            Los productos de calidad <strong>rinden más y cuidan mejor</strong> tu piel
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- CTA --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
        <tr>
            <td align="center" style="padding:8px 0;">
                <a href="{{ route('products.index') }}"
                   style="display:inline-block;background-color:#D9B56D;color:#FFFFFF;font-size:15px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:8px;">
                    Ver productos
                </a>
            </td>
        </tr>
    </table>

    {{-- Secondary CTA --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:0 0 8px;">
                <a href="{{ route('landing.quiz') }}"
                   style="display:inline-block;color:#D9B56D;font-size:14px;font-weight:600;text-decoration:none;padding:10px 24px;border:1px solid #D9B56D;border-radius:8px;">
                    ¿Qué necesita tu piel? — Hacer quiz
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0;font-size:13px;color:#9CA3AF;text-align:center;">
        ¿Tienes preguntas? Responde a este correo y te ayudaremos.
    </p>
@endsection
