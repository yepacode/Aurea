@extends('layouts.app')

@section('title', 'Gracias por tu opinión · Belleza Áurea')
@section('robots', 'noindex, nofollow')

@php
    $score = $nps->score;
    $bucket = $nps->bucket();
    // Mensaje ajustado al bucket para que se sienta humano.
    $heading = match($bucket) {
        'promoter'  => '¡Gracias, nos hiciste el día! 💛',
        'passive'   => 'Gracias por tu tiempo 🌸',
        'detractor' => 'Gracias por contarnos 💛',
        default     => 'Gracias por tu opinión 🌸',
    };
    $body = match($bucket) {
        'promoter'  => 'Nos encanta saber que la experiencia fue tan bonita. Compártela con quien quieras, la seguimos cuidando en cada pedido.',
        'passive'   => 'Nos motiva a seguir mejorando en cada detalle. Estamos trabajando para que la próxima experiencia te enamore.',
        'detractor' => 'Sentimos que la experiencia no fue la que esperabas. Alguien del equipo va a revisar tu comentario personalmente para hacerlo mejor.',
        default     => 'Tu opinión ya llegó a nuestro equipo. Cada respuesta nos ayuda a cuidar más a las clientas Áurea.',
    };
@endphp

@push('head')
<style>
    .npt-wrap{background:#FBF8F2;min-height:70vh;padding:64px 16px;display:flex;align-items:center;justify-content:center;}
    .npt-card{max-width:560px;width:100%;background:#fff;border-radius:16px;text-align:center;
              box-shadow:0 8px 32px rgba(46,42,38,.08);padding:48px 32px;
              border-top:4px solid;border-image:linear-gradient(90deg,#2E2A26,#D9B56D) 1;}
    .npt-badge{display:inline-flex;align-items:center;justify-content:center;
               width:72px;height:72px;border-radius:50%;font-size:32px;font-weight:800;color:#fff;
               margin:0 auto 20px;background:#D9B56D;}
    .npt-title{font-family:'Playfair Display',Georgia,serif;font-size:28px;color:#2E2A26;margin:0 0 12px;}
    .npt-body{font-size:15px;line-height:1.7;color:#4B5563;margin:0 0 28px;}
    .npt-cta{display:inline-block;background:#2E2A26;color:#fff;font-weight:600;font-size:14px;
             text-decoration:none;border-radius:999px;padding:14px 32px;transition:background .15s ease;}
    .npt-cta:hover{background:#1a1815;color:#fff;}
    .npt-link{display:inline-block;margin-left:12px;color:#D9B56D;text-decoration:none;font-size:14px;}
    .npt-link:hover{text-decoration:underline;color:#D9B56D;}
</style>
@endpush

@section('content')
<div class="npt-wrap">
    <div class="npt-card">
        @if($score !== null)
            <div class="npt-badge">{{ $score }}</div>
        @else
            <div class="npt-badge">💛</div>
        @endif
        <h1 class="npt-title">{{ $heading }}</h1>
        <p class="npt-body">{{ $body }}</p>
        <a href="{{ route('products.index') }}" class="npt-cta">Volver a la tienda</a>
        <a href="{{ route('home') }}" class="npt-link">Ir al inicio</a>
    </div>
</div>
@endsection
