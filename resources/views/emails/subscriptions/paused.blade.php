@extends('layouts.email')

@section('title', 'Ritual en pausa')

@section('content')
    <h1 style="margin:0 0 12px;font-family:'Georgia',serif;font-size:26px;color:#2E2A26;">Tu ritual está en pausa 💛</h1>
    <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.6;">
        Hola <strong>{{ $subscription->customer->name }}</strong>, hemos pausado tu suscripción a
        <strong style="color:#2E2A26;">{{ $subscription->plan->name }}</strong>.
        No enviaremos entregas hasta que la reanudes.
    </p>
    @if($subscription->pause_reason)
        <p style="margin:0 0 16px;font-size:13px;color:#8B7B5C;">
            Motivo: {{ $subscription->pause_reason }}
        </p>
    @endif
    <p style="margin:16px 0 8px;font-size:14px;color:#4B5563;">
        Puedes reanudarla cuando quieras desde
        <a href="{{ route('account.subscriptions') }}" style="color:#D9B56D;">Mi cuenta</a>.
    </p>
    <p style="margin:0;font-size:14px;color:#4B5563;">Con cariño, el equipo de Belleza Áurea 💛</p>
@endsection
