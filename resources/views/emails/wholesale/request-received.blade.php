@extends('layouts.email')

@section('title', 'Nueva solicitud de mayorista — Belleza Áurea')

@section('content')
    <h1 style="margin:0 0 8px;font-family:Georgia,'Times New Roman',serif;font-size:24px;font-weight:600;color:#2E2A26;">
        Nueva solicitud de mayorista 🏪
    </h1>
    <p style="margin:0 0 24px;color:#6B6157;font-size:15px;line-height:1.5;">
        Una clienta acaba de solicitar entrar al programa de distribuidoras.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#FBF8F2;border:1px solid #E5DCC9;border-radius:12px;margin-bottom:20px;">
        <tr>
            <td style="padding:20px 22px;">
                <p style="margin:0 0 10px;font-size:12px;color:#8A6E2E;text-transform:uppercase;letter-spacing:0.14em;font-weight:700;">
                    Datos de la solicitud
                </p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#2E2A26;">
                    <tr>
                        <td style="padding:4px 0;color:#6B6157;width:40%;">Nombre:</td>
                        <td style="padding:4px 0;font-weight:600;">{{ $customer->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6B6157;">Correo:</td>
                        <td style="padding:4px 0;font-weight:600;">
                            <a href="mailto:{{ $customer->email }}" style="color:#BE9A53;text-decoration:none;">{{ $customer->email }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6B6157;">Teléfono:</td>
                        <td style="padding:4px 0;font-weight:600;">{{ $customer->phone ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6B6157;">Razón social:</td>
                        <td style="padding:4px 0;font-weight:600;">{{ $customer->wholesaler_company_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6B6157;">NIT:</td>
                        <td style="padding:4px 0;font-weight:600;">{{ $customer->wholesaler_nit ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6B6157;">Ciudad:</td>
                        <td style="padding:4px 0;font-weight:600;">{{ $customer->city ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6B6157;">Volumen estimado:</td>
                        <td style="padding:4px 0;font-weight:600;">
                            {{ config('wholesale.monthly_volume_options.'.$customer->wholesaler_monthly_volume, $customer->wholesaler_monthly_volume ?: '—') }}
                        </td>
                    </tr>
                </table>

                @if($customer->wholesaler_notes)
                    <p style="margin:16px 0 4px;font-size:12px;color:#8A6E2E;text-transform:uppercase;letter-spacing:0.14em;font-weight:700;">
                        Comentario de la clienta
                    </p>
                    <p style="margin:0;color:#2E2A26;font-size:14px;white-space:pre-wrap;">{{ $customer->wholesaler_notes }}</p>
                @endif
            </td>
        </tr>
    </table>

    <p style="text-align:center;margin:24px 0 8px;">
        <a href="{{ route('admin.wholesale.index') }}"
           style="display:inline-block;padding:13px 28px;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);color:#3B310F;font-weight:700;font-size:14px;text-decoration:none;border-radius:9999px;">
            Revisar en el panel →
        </a>
    </p>

    <p style="margin:24px 0 0;font-size:13px;color:#9CA3AF;text-align:center;">
        Solicitud enviada el {{ $customer->wholesaler_requested_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}.
    </p>
@endsection
