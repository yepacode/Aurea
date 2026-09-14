@extends('layouts.app')

@section('title', 'Tu opinión importa · Belleza Áurea')
@section('meta_description', 'Cuéntanos cómo te fue con tu compra en Belleza Áurea. Tu opinión nos ayuda a mejorar.')
@section('robots', 'noindex, nofollow')

@php
    // Gradiente rojo → amarillo → verde para los 11 botones (0..10)
    $palette = [
        0  => '#B91C1C', 1  => '#C1272D', 2  => '#D9302B', 3  => '#E85B24',
        4  => '#F0851F', 5  => '#F2B01E', 6  => '#E8C227', 7  => '#C7CE2F',
        8  => '#9CBF3A', 9  => '#5EAE47', 10 => '#2E9E48',
    ];
    $firstName = $nps->order?->customer?->name
        ? preg_split('/\s+/', trim($nps->order->customer->name))[0]
        : null;
@endphp

@push('head')
<style>
    .nps-wrap{background:#FBF8F2;min-height:70vh;padding:56px 16px;}
    .nps-card{max-width:720px;margin:0 auto;background:#fff;border-radius:16px;
              box-shadow:0 8px 32px rgba(46,42,38,.08);
              border-top:4px solid;border-image:linear-gradient(90deg,#2E2A26,#D9B56D) 1;
              overflow:hidden;}
    .nps-card__body{padding:40px 32px;}
    .nps-title{font-family:'Playfair Display',Georgia,serif;font-size:28px;line-height:1.25;color:#2E2A26;margin:0 0 8px;}
    .nps-sub{margin:0 0 24px;font-size:15px;line-height:1.6;color:#4B5563;}
    .nps-question{margin:24px 0 12px;font-size:16px;color:#2E2A26;font-weight:600;}
    .nps-scale{display:grid;grid-template-columns:repeat(11,minmax(0,1fr));gap:6px;margin:12px 0 8px;}
    .nps-btn{appearance:none;border:none;cursor:pointer;color:#fff;font-weight:700;font-size:16px;
             border-radius:8px;padding:14px 0;transition:transform .12s ease,box-shadow .12s ease,outline .12s ease;
             outline:2px solid transparent;outline-offset:2px;}
    .nps-btn:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.15);}
    .nps-btn.is-active{outline:2px solid #2E2A26;box-shadow:0 0 0 4px rgba(217,181,109,.35);}
    .nps-anchors{display:flex;justify-content:space-between;font-size:12px;color:#9CA3AF;margin:0 2px 20px;}
    .nps-field{display:block;width:100%;padding:12px 14px;border:1px solid #E5E7EB;border-radius:10px;
               font-size:15px;color:#2E2A26;background:#fff;resize:vertical;min-height:110px;
               font-family:inherit;}
    .nps-field:focus{outline:2px solid #D9B56D;outline-offset:1px;border-color:#D9B56D;}
    .nps-label{display:block;margin:8px 0 6px;font-size:13px;color:#6B7280;}
    .nps-submit{display:inline-block;background:#2E2A26;color:#fff;font-weight:600;font-size:15px;
                border:none;border-radius:999px;padding:14px 32px;cursor:pointer;margin-top:20px;
                transition:background .15s ease;}
    .nps-submit:hover{background:#1a1815;}
    .nps-submit:disabled{background:#9CA3AF;cursor:not-allowed;}
    .nps-meta{margin-top:24px;font-size:12px;color:#9CA3AF;text-align:center;}
    @media (max-width:520px){
        .nps-card__body{padding:28px 20px;}
        .nps-title{font-size:22px;}
        .nps-btn{font-size:14px;padding:12px 0;}
        .nps-scale{gap:4px;}
    }
</style>
@endpush

@section('content')
<div class="nps-wrap">
    <div class="nps-card">
        <div class="nps-card__body">
            <h1 class="nps-title">
                {{ $firstName ? "Hola $firstName, " : '' }}cuéntanos cómo te fue 🌸
            </h1>
            <p class="nps-sub">
                Estás a un clic de ayudarnos a mejorar. Recuerdas tu pedido
                <strong>#{{ $nps->order?->id }}</strong>@if($nps->order?->created_at) del {{ $nps->order->created_at->translatedFormat('d \d\e F, Y') }}@endif?
                Tu respuesta es anónima para el resto del mundo y llega directo a nuestro equipo.
            </p>

            <form method="POST" action="{{ route('nps.submit', ['token' => $nps->token]) }}" x-data="{ score: @js($preselect) }">
                @csrf

                <p class="nps-question">
                    En una escala del 0 al 10, ¿qué tan probable es que nos recomiendes a una amiga?
                </p>

                <div class="nps-scale" role="radiogroup" aria-label="Puntuación NPS">
                    @foreach(range(0, 10) as $n)
                        <button type="button"
                                class="nps-btn"
                                :class="{ 'is-active': score === {{ $n }} }"
                                @click="score = {{ $n }}"
                                style="background:{{ $palette[$n] }};"
                                aria-label="Puntuación {{ $n }} de 10">
                            {{ $n }}
                        </button>
                    @endforeach
                </div>

                <div class="nps-anchors">
                    <span>0 · Nada probable</span>
                    <span>10 · Muy probable</span>
                </div>

                <input type="hidden" name="score" :value="score">

                <label class="nps-label" for="nps-comment">
                    ¿Quieres contarnos algo más? (opcional)
                </label>
                <textarea id="nps-comment"
                          name="comment"
                          class="nps-field"
                          maxlength="2000"
                          placeholder="Qué te gustó, qué mejorarías, cualquier detalle nos ayuda…"></textarea>

                @error('score')
                    <p style="color:#B91C1C;font-size:13px;margin-top:8px;">{{ $message }}</p>
                @enderror
                @error('comment')
                    <p style="color:#B91C1C;font-size:13px;margin-top:8px;">{{ $message }}</p>
                @enderror

                <div style="text-align:center;">
                    <button type="submit"
                            class="nps-submit"
                            :disabled="score === null"
                            x-text="score === null ? 'Elige una puntuación' : 'Enviar mi opinión'"></button>
                </div>
            </form>

            <p class="nps-meta">
                Belleza Áurea · Gracias por tomarte estos segundos 💛
            </p>
        </div>
    </div>
</div>
@endsection
