@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('robots', $seo['robots'])
@section('canonical', $seo['canonical'])
@if(!empty($seo['keywords']))@section('keywords', $seo['keywords'])@endif
@section('og_type', $seo['og_type'])
@section('og_title', $seo['og_title'])
@section('og_description', $seo['og_description'])
@section('og_image', $seo['og_image'])
@section('twitter_card', $seo['twitter_card'])
@section('twitter_title', $seo['twitter_title'])
@section('twitter_description', $seo['twitter_description'])
@section('twitter_image', $seo['twitter_image'])
@if(!empty($seo['custom_schema']))@section('custom_schema', $seo['custom_schema'])@endif

@push('schema')
<script type="application/ld+json">{!! $brandSchema !!}</script>
@endpush

@section('content')
<main>
    {{-- HERO --}}
    <section style="position:relative;overflow:hidden;{{ $brand->banner_path ? '' : 'background:radial-gradient(circle at 30% 30%, #FBF4E6 0%, #F7F3ED 50%, #E8D1C5 100%);' }}">
        @if($brand->banner_path)
        <div style="position:absolute;inset:0;background:url('{{ $brand->banner_url }}') center/cover;"></div>
        <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(46,42,38,.35) 0%, rgba(46,42,38,.65) 100%);"></div>
        @endif

        <div class="ba-container" style="position:relative;padding:clamp(80px,12vw,160px) 0;text-align:center;color:{{ $brand->banner_path ? '#FFFFFF' : '#2E2A26' }};">
            <nav style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;margin-bottom:28px;color:{{ $brand->banner_path ? 'rgba(255,255,255,.65)' : '#B8A999' }};">
                <a href="{{ url('/') }}" style="color:inherit;">Inicio</a>
                <span style="margin:0 8px;">/</span>
                <a href="{{ route('brands.index') }}" style="color:inherit;">Marcas</a>
                <span style="margin:0 8px;">/</span>
                <span style="color:{{ $brand->banner_path ? '#FFFFFF' : '#2E2A26' }};">{{ $brand->name }}</span>
            </nav>

            @if($brand->logo_path)
            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-height:120px;margin:0 auto 28px;display:block;{{ $brand->banner_path ? 'filter:brightness(0) invert(1);' : '' }}">
            @endif

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(42px,5.5vw,72px);font-weight:500;line-height:1.05;margin:0 0 18px;">
                {{ $brand->name }}
            </h1>

            @if($brand->country_origin)
            <p style="font-size:11px;letter-spacing:0.28em;text-transform:uppercase;margin:0 0 24px;color:{{ $brand->banner_path ? 'rgba(255,255,255,.7)' : '#B8A999' }};">
                Origen · {{ $brand->country_origin }}
            </p>
            @endif

            @if($brand->short_description)
            <p style="max-width:680px;margin:0 auto;font-size:17px;line-height:1.7;color:{{ $brand->banner_path ? 'rgba(255,255,255,.85)' : '#6B6157' }};">
                {{ $brand->short_description }}
            </p>
            @endif

            @if($brand->website_url)
            <a href="{{ $brand->website_url }}" target="_blank" rel="noopener nofollow"
               style="display:inline-flex;align-items:center;gap:8px;margin-top:32px;font-size:13px;font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:{{ $brand->banner_path ? '#FFFFFF' : '#BE9A53' }};text-decoration:none;border-bottom:1px solid {{ $brand->banner_path ? '#FFFFFF' : '#D9B56D' }};padding-bottom:4px;">
                Sitio oficial
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3h7v7m0-7L10 14m-3-1v8H3v-8h8"/></svg>
            </a>
            @endif
        </div>
    </section>

    {{-- DESCRIPCIÓN LARGA (SEO) --}}
    @if($brand->long_description)
    <section style="background:#FFFFFF;padding:clamp(64px,8vw,100px) 0;">
        <div class="ba-container" style="max-width:760px;">
            <div style="font-size:16px;line-height:1.85;color:#2E2A26;font-family:'Montserrat',sans-serif;">
                {!! nl2br(e($brand->long_description)) !!}
            </div>
        </div>
    </section>
    @endif

    {{-- PRODUCTOS DE LA MARCA --}}
    <section style="background:#FBF8F2;padding:clamp(64px,8vw,120px) 0;">
        <div class="ba-container">
            <header style="text-align:center;margin-bottom:64px;">
                <span style="font-size:11px;letter-spacing:0.28em;text-transform:uppercase;color:#BE9A53;font-weight:500;">Catálogo</span>
                <h2 style="font-family:'Playfair Display',serif;font-size:clamp(28px,4vw,44px);font-weight:500;color:#2E2A26;margin:12px 0 0;">
                    Productos de {{ $brand->name }}
                </h2>
                <div style="width:36px;height:1px;background:#D9B56D;margin:24px auto 0;"></div>
            </header>

            @if($products->isNotEmpty())
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:32px 24px;">
                @foreach($products as $p)
                <a href="{{ route('products.show', $p->slug) }}" style="display:block;text-decoration:none;color:inherit;">
                    <div style="aspect-ratio:4/5;background:#FBF8F2;border-radius:2px;overflow:hidden;margin-bottom:18px;position:relative;">
                        @if(!empty($p->images))
                            <img src="{{ asset('storage/'.$p->images[0]) }}" alt="{{ $p->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;transition:transform 1.2s ease;">
                        @else
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#D9B56D;font-family:'Playfair Display',serif;font-style:italic;">
                                Próximamente
                            </div>
                        @endif
                    </div>
                    @if($p->category)
                    <p style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:#B8A999;margin:0 0 6px;">{{ $p->category->name }}</p>
                    @endif
                    <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:500;color:#2E2A26;margin:0 0 10px;line-height:1.25;">{{ $p->name }}</h3>
                    <div style="display:flex;align-items:baseline;gap:10px;">
                        <span style="font-size:16px;font-weight:500;color:#2E2A26;">${{ number_format($p->price, 0, ',', '.') }}</span>
                        @if($p->compare_price && $p->compare_price > $p->price)
                        <span style="font-size:13px;color:#B8A999;text-decoration:line-through;">${{ number_format($p->compare_price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            <div style="margin-top:48px;">{{ $products->links() }}</div>
            @else
            <p style="text-align:center;color:#6B6157;">Próximamente catálogo de {{ $brand->name }}.</p>
            @endif
        </div>
    </section>
</main>
@endsection
