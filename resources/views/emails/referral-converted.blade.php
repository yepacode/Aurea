@extends('layouts.email')

@section('title', '¡Ganaste puntos en Belleza Áurea!')

@section('content')
    <h1 style="margin:0 0 8px;font-size:24px;font-weight:700;color:#1A1A2E;">
        ¡Felicidades{{ $referrer && $referrer->name ? ', ' . $referrer->name : '' }}! 💛
    </h1>
    <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.6;">
        Tu amiga <strong>{{ $referred->name ?? 'una amiga' }}</strong> ya hizo su primera compra en
        Belleza Áurea gracias a tu recomendación. Como agradecimiento, acabamos de
        acreditar puntos a tu cuenta.
    </p>

    {{-- Bloque de puntos --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background-color:#FBF8F2;border:1px solid #F0E4C3;border-radius:12px;margin:8px 0 28px;">
        <tr>
            <td align="center" style="padding:24px 16px;">
                <p style="margin:0;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#8A6E2E;font-weight:700;">
                    Puntos ganados
                </p>
                <p style="margin:6px 0 0;font-size:40px;font-weight:800;color:#7A5E1C;line-height:1;">
                    +{{ number_format($pointsEarned, 0, ',', '.') }} ⭐
                </p>
                <p style="margin:8px 0 0;font-size:13px;color:#8A6E2E;">
                    Ya están disponibles en tu cuenta.
                </p>
            </td>
        </tr>
    </table>

    {{-- CTA --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td align="center" style="padding:8px 0;">
                <a href="{{ route('account.loyalty') }}"
                   style="display:inline-block;background-color:#D9B56D;color:#FFFFFF;font-size:15px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:8px;">
                    Ver mis puntos
                </a>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:0 0 8px;">
                <a href="{{ route('account.referrals') }}"
                   style="display:inline-block;color:#D9B56D;font-size:14px;font-weight:600;text-decoration:none;padding:10px 24px;border:1px solid #D9B56D;border-radius:8px;">
                    Sigue recomendando →
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0;font-size:13px;color:#9CA3AF;text-align:center;">
        Puedes seguir compartiendo tu código con más amigas: cada compra pagada te suma puntos.
    </p>
@endsection
