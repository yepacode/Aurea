@extends('layouts.email')

@section('title', '¿Cómo te fue con tu compra? · Belleza Áurea')

@php
    $firstName = $customer && $customer->name
        ? preg_split('/\s+/', trim($customer->name))[0]
        : 'Hola';
    $orderDate = $order?->created_at?->translatedFormat('d \d\e F, Y');

    // Gradiente rojo → amarillo → verde para los 11 botones (0..10)
    $palette = [
        0  => '#B91C1C', // rojo intenso
        1  => '#C1272D',
        2  => '#D9302B',
        3  => '#E85B24',
        4  => '#F0851F',
        5  => '#F2B01E', // amarillo neutro
        6  => '#E8C227',
        7  => '#C7CE2F',
        8  => '#9CBF3A',
        9  => '#5EAE47',
        10 => '#2E9E48', // verde intenso
    ];
@endphp

@section('content')
    <h1 style="margin:0 0 16px;font-family:'Playfair Display',Georgia,serif;font-size:24px;line-height:1.3;color:#2E2A26;">
        Hola {{ $firstName }}, ¿cómo te fue con tu pedido? 💛
    </h1>

    <p style="margin:0 0 12px;font-size:16px;line-height:1.6;color:#4B5563;">
        Ya han pasado unos días desde que recibiste tu pedido
        <strong>#{{ $order?->id }}</strong>@if($orderDate) del <strong>{{ $orderDate }}</strong>@endif,
        y en <strong>Belleza Áurea</strong> queremos saber cómo te fue con tu compra.
    </p>

    <p style="margin:0 0 24px;font-size:16px;line-height:1.6;color:#4B5563;">
        <strong>En una escala del 0 al 10</strong>, ¿qué tan probable es que nos recomiendes a una amiga?
    </p>

    {{-- Bloque de 11 botones con degradado rojo → amarillo → verde --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
        <tr>
            @foreach(range(0, 10) as $n)
                <td align="center" style="padding:3px;">
                    <a href="{{ route('nps.respond', ['token' => $token, 'score' => $n]) }}"
                       style="display:block;width:36px;height:36px;line-height:36px;
                              font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;
                              text-align:center;text-decoration:none;color:#FFFFFF;
                              background:{{ $palette[$n] }};border-radius:6px;">
                        {{ $n }}
                    </a>
                </td>
            @endforeach
        </tr>
        <tr>
            <td colspan="11" style="padding:6px 2px 0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="left" style="font-size:11px;color:#9CA3AF;font-family:Arial,Helvetica,sans-serif;">
                            0 — Nada probable
                        </td>
                        <td align="right" style="font-size:11px;color:#9CA3AF;font-family:Arial,Helvetica,sans-serif;">
                            10 — Muy probable
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#6B7280;">
        Al hacer clic en un número te llevaremos a una página donde podrás dejarnos también
        un comentario si quieres. Tu opinión nos ayuda a mejorar cada día.
    </p>

    <p style="margin:0 0 8px;font-size:15px;color:#4B5563;">
        ¡Gracias por ser parte de la familia Áurea! 🌸
    </p>
    <p style="margin:0;font-size:15px;color:#4B5563;">
        <strong>Belleza Áurea</strong>
    </p>
@endsection
