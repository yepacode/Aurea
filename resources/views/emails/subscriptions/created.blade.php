@extends('layouts.email')

@section('title', '¡Bienvenida a tu ritual Áurea!')

@section('content')
    <h1 style="margin:0 0 12px;font-family:'Georgia',serif;font-size:26px;color:#2E2A26;">🌸 ¡Bienvenida a tu ritual Áurea!</h1>
    <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.6;">
        Hola <strong>{{ $subscription->customer->name }}</strong>, tu suscripción a
        <strong style="color:#2E2A26;">{{ $subscription->plan->name }}</strong> ya está activa.
    </p>
    <p style="margin:0 0 24px;font-size:15px;color:#4B5563;line-height:1.6;">
        Tu primer ritual llega el
        <strong style="color:#D9B56D;">{{ $subscription->next_delivery_at->translatedFormat('l j \d\e F') }}</strong>.
        Nosotras nos encargamos de todo — cada {{ $subscription->plan->interval_days }} días recibirás tu selección
        con tu descuento de suscriptora aplicado.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FBF8F2;border-radius:8px;">
        <tr><td style="padding:20px 24px;">
            <p style="margin:0 0 10px;font-size:13px;color:#8B7B5C;text-transform:uppercase;letter-spacing:1px;">Tu ritual incluye</p>
            @foreach($subscription->plan->products as $product)
                <p style="margin:6px 0;font-size:14px;color:#2E2A26;">
                    ✓ {{ $product->name }} <span style="color:#8B7B5C;">× {{ $product->pivot->quantity }}</span>
                </p>
            @endforeach
        </td></tr>
    </table>

    <p style="margin:24px 0 8px;font-size:14px;color:#4B5563;">
        Puedes pausar o cancelar cuando quieras desde
        <a href="{{ route('account.subscriptions') }}" style="color:#D9B56D;">Mi cuenta</a>.
    </p>
    <p style="margin:0;font-size:14px;color:#4B5563;">Con cariño, el equipo de Belleza Áurea 💛</p>
@endsection
