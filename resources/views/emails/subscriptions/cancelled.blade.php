@extends('layouts.email')

@section('title', 'Ritual cancelado')

@section('content')
    <h1 style="margin:0 0 12px;font-family:'Georgia',serif;font-size:26px;color:#2E2A26;">Hasta pronto 💛</h1>
    <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.6;">
        Hola <strong>{{ $subscription->customer->name }}</strong>, tu suscripción a
        <strong style="color:#2E2A26;">{{ $subscription->plan->name }}</strong> quedó cancelada.
        No recibirás más entregas automáticas.
    </p>
    <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.6;">
        Gracias por haber sido parte de nuestro ritual. Siempre puedes volver cuando quieras —
        <a href="{{ route('subscriptions.index') }}" style="color:#D9B56D;">nuestros planes te esperan</a>.
    </p>
    <p style="margin:0;font-size:14px;color:#4B5563;">Con cariño, el equipo de Belleza Áurea 💛</p>
@endsection
