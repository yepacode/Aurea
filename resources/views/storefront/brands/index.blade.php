@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('canonical', $seo['canonical'])
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])
@section('og_image', $seo['og_image'] ?? asset('img/brand/logo-principal.png'))
@section('twitter_title', $seo['title'])
@section('twitter_description', $seo['description'])
@section('twitter_image', $seo['og_image'] ?? asset('img/brand/logo-principal.png'))
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])

@section('content')
<main style="background:#FBF8F2;padding:clamp(72px,10vw,140px) 0;">
    <div class="ba-container">
        <header style="text-align:center;margin-bottom:clamp(48px,7vw,88px);">
            <span style="display:block;font-size:11px;letter-spacing:0.28em;text-transform:uppercase;color:#BE9A53;margin-bottom:16px;font-weight:500;">Distribuimos</span>
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(36px,5vw,58px);font-weight:500;line-height:1.08;color:#2E2A26;margin:0 0 18px;">
                Marcas que <em style="color:#D9B56D;font-style:italic;">amamos</em>
            </h1>
            <p style="max-width:560px;margin:0 auto;font-size:16px;line-height:1.7;color:#6B6157;">
                Trabajamos con marcas premium de cosmética, skincare y rituales seleccionadas con criterio: ingredientes limpios, formulaciones efectivas y packaging cuidado.
            </p>
            <div style="width:36px;height:1px;background:#D9B56D;margin:24px auto 0;"></div>
        </header>

        @if($brands->isNotEmpty())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px;">
            @foreach($brands as $brand)
                <a href="{{ route('brands.show', $brand->slug) }}"
                   style="display:flex;flex-direction:column;align-items:center;padding:36px 24px;background:#FFFFFF;border-radius:2px;text-decoration:none;border:1px solid rgba(184,169,153,.2);transition:transform .35s ease, box-shadow .35s ease;"
                   onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 20px 40px rgba(190,154,83,.1)';"
                   onmouseout="this.style.transform='none';this.style.boxShadow='none';">
                    <div style="height:90px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
                        @if($brand->logo_path)
                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-height:90px;max-width:100%;object-fit:contain;">
                        @else
                            <span style="font-family:'Playfair Display',serif;font-size:32px;color:#D9B56D;font-weight:500;letter-spacing:.04em;">{{ \Str::upper(\Str::substr($brand->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <h2 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:500;color:#2E2A26;margin:0 0 6px;text-align:center;">{{ $brand->name }}</h2>
                    @if($brand->country_origin)
                    <p style="font-size:10px;text-transform:uppercase;letter-spacing:.18em;color:#B8A999;margin:0 0 14px;">{{ $brand->country_origin }}</p>
                    @endif
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:#BE9A53;margin-top:auto;">
                        {{ $brand->active_products_count }} producto(s)
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
        @else
        <p style="text-align:center;color:#6B6157;">Próximamente.</p>
        @endif
    </div>
</main>
@endsection
