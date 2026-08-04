@extends('layouts.app')

@section('title', $seoSettings->meta_title ?? 'Sobre nosotras | Belleza Áurea')
@section('meta_description', $seoSettings->meta_description ?? 'Conoce la historia de Belleza Áurea, distribuidora colombiana de cosméticos e insumos de belleza originales, con calidad garantizada y envíos a todo el país.')
@section('canonical', route('about'))
@section('og_title', $seoSettings->og_title ?? $seoSettings->meta_title ?? 'Sobre nosotras | Belleza Áurea')
@section('og_description', $seoSettings->og_description ?? $seoSettings->meta_description ?? 'Distribuidora colombiana de cosméticos e insumos de belleza. Calidad, autenticidad y buenos precios.')
@section('twitter_title', $seoSettings->twitter_title ?? $seoSettings->meta_title ?? 'Sobre nosotras | Belleza Áurea')
@section('twitter_description', $seoSettings->twitter_description ?? $seoSettings->meta_description ?? 'Distribuidora colombiana de cosméticos e insumos de belleza.')
@section('og_image', ($seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))
@section('twitter_image', ($seoSettings->twitter_image_url ?? $seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))

@section('content')

    {{-- ══════════════ HERO — Nuestra historia ══════════════ --}}
    <section style="position:relative;overflow:hidden;background:linear-gradient(180deg,#FBF8F2 0%,#F7F3ED 100%);">
        {{-- Resplandores áureos decorativos --}}
        <div aria-hidden="true" style="position:absolute;top:-120px;right:-100px;width:380px;height:380px;border-radius:50%;filter:blur(90px);background:radial-gradient(circle,rgba(217,181,109,0.20),transparent 70%);pointer-events:none;"></div>
        <div aria-hidden="true" style="position:absolute;bottom:-140px;left:-100px;width:420px;height:420px;border-radius:50%;filter:blur(90px);background:radial-gradient(circle,rgba(168,178,154,0.16),transparent 70%);pointer-events:none;"></div>

        <div style="position:relative;max-width:820px;margin:0 auto;padding:clamp(64px,10vw,128px) clamp(24px,5vw,48px);text-align:center;z-index:1;">
            {{-- Eyebrow --}}
            <span style="display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:500;letter-spacing:0.24em;text-transform:uppercase;color:#BE9A53;margin-bottom:26px;">
                <span aria-hidden="true" style="display:block;width:32px;height:1px;background:#D9B56D;"></span>
                Nuestra historia
                <span aria-hidden="true" style="display:block;width:32px;height:1px;background:#D9B56D;"></span>
            </span>

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(36px,5vw,64px);font-weight:500;line-height:1.08;letter-spacing:-0.015em;color:#2E2A26;margin:0 0 28px;">
                Belleza que realza <em style="font-style:italic;color:#D9B56D;">tu esencia</em>
            </h1>

            <p style="font-size:clamp(16px,1.6vw,18px);line-height:1.75;color:#6B6157;max-width:620px;margin:0 auto;">
                Belleza Áurea nació en Colombia con un propósito claro: acercar a cada mujer productos de
                cosmética e insumos de belleza de la más alta calidad, sin intermediarios que inflen los precios.
                Somos una distribuidora comprometida con la autenticidad, la confianza y el trato cercano,
                porque creemos que cuidarte debe ser un ritual accesible, seguro y placentero.
            </p>
        </div>
    </section>

    @include('partials.ba-divider')

    {{-- ══════════════ NUESTRA ESENCIA — Valores ══════════════ --}}
    <section style="background:#FBF8F2;padding:clamp(64px,9vw,120px) 0;">
        <div style="max-width:1200px;margin:0 auto;padding:0 clamp(24px,5vw,48px);">

            {{-- Encabezado de sección --}}
            <div style="text-align:center;margin-bottom:clamp(40px,6vw,72px);">
                <span style="display:block;font-size:11px;letter-spacing:0.28em;text-transform:uppercase;color:#BE9A53;font-weight:500;margin-bottom:14px;">Nuestra esencia</span>
                <h2 style="font-family:'Playfair Display',serif;font-size:clamp(28px,3.6vw,44px);font-weight:500;line-height:1.15;color:#2E2A26;margin:0 auto;max-width:640px;">
                    Los valores que nos mueven
                </h2>
                <div aria-hidden="true" style="width:36px;height:1px;background:#D9B56D;margin:22px auto 0;"></div>
            </div>

            {{-- Grid de tarjetas de valor --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:24px;">

                {{-- Calidad garantizada --}}
                <article style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 20px 48px -32px rgba(46,42,38,0.45);">
                    <span aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#FBF4E6 0%,#F7F3ED 100%);color:#BE9A53;margin-bottom:22px;">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                    <h3 style="font-family:'Playfair Display',serif;font-size:19px;font-weight:500;color:#2E2A26;margin:0 0 12px;">Calidad garantizada</h3>
                    <p style="font-size:14px;line-height:1.7;color:#6B6157;margin:0;">Seleccionamos cada producto con criterio profesional para que sientas la diferencia desde el primer uso.</p>
                </article>

                {{-- Productos 100% originales --}}
                <article style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 20px 48px -32px rgba(46,42,38,0.45);">
                    <span aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#FBF4E6 0%,#F7F3ED 100%);color:#BE9A53;margin-bottom:22px;">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                    </span>
                    <h3 style="font-family:'Playfair Display',serif;font-size:19px;font-weight:500;color:#2E2A26;margin:0 0 12px;">Productos 100% originales</h3>
                    <p style="font-size:14px;line-height:1.7;color:#6B6157;margin:0;">Trabajamos solo con marcas y proveedores auténticos. Nada de imitaciones: tu piel merece lo real.</p>
                </article>

                {{-- Envío a toda Colombia --}}
                <article style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 20px 48px -32px rgba(46,42,38,0.45);">
                    <span aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#FBF4E6 0%,#F7F3ED 100%);color:#BE9A53;margin-bottom:22px;">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-6.75m0 0V6.375c0-.621-.504-1.125-1.125-1.125H4.5m0 0h-.375A1.125 1.125 0 0 0 3 6.375v6.75m1.5-9V4.5m0 0h9.75c.621 0 1.125.504 1.125 1.125V9"/></svg>
                    </span>
                    <h3 style="font-family:'Playfair Display',serif;font-size:19px;font-weight:500;color:#2E2A26;margin:0 0 12px;">Envío a toda Colombia</h3>
                    <p style="font-size:14px;line-height:1.7;color:#6B6157;margin:0;">Llevamos tu belleza favorita hasta la puerta de tu casa, estés donde estés en el país.</p>
                </article>

                {{-- Atención cercana --}}
                <article style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 20px 48px -32px rgba(46,42,38,0.45);">
                    <span aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#FBF4E6 0%,#F7F3ED 100%);color:#BE9A53;margin-bottom:22px;">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                    </span>
                    <h3 style="font-family:'Playfair Display',serif;font-size:19px;font-weight:500;color:#2E2A26;margin:0 0 12px;">Atención cercana</h3>
                    <p style="font-size:14px;line-height:1.7;color:#6B6157;margin:0;">Te acompañamos con asesoría honesta y trato humano, antes, durante y después de tu compra.</p>
                </article>

            </div>
        </div>
    </section>

    {{-- ══════════════ POR QUÉ ELEGIRNOS — Misión y visión ══════════════ --}}
    <section style="background:#F7F3ED;padding:clamp(64px,9vw,120px) 0;">
        <div style="max-width:1080px;margin:0 auto;padding:0 clamp(24px,5vw,48px);">

            <div style="text-align:center;margin-bottom:clamp(40px,6vw,64px);">
                <span style="display:block;font-size:11px;letter-spacing:0.28em;text-transform:uppercase;color:#BE9A53;font-weight:500;margin-bottom:14px;">Por qué elegirnos</span>
                <h2 style="font-family:'Playfair Display',serif;font-size:clamp(28px,3.6vw,44px);font-weight:500;line-height:1.15;color:#2E2A26;margin:0 auto;max-width:640px;">
                    Más que una tienda, una aliada de tu belleza
                </h2>
                <div aria-hidden="true" style="width:36px;height:1px;background:#D9B56D;margin:22px auto 0;"></div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;">

                {{-- Misión --}}
                <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(32px,4vw,44px);box-shadow:0 20px 48px -34px rgba(46,42,38,0.4);">
                    <span style="display:block;font-family:'Playfair Display',serif;font-style:italic;font-size:13px;letter-spacing:0.06em;color:#BE9A53;margin-bottom:12px;">Nuestra misión</span>
                    <h3 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:500;color:#2E2A26;margin:0 0 16px;line-height:1.25;">Belleza accesible, sin renunciar a la calidad</h3>
                    <p style="font-size:15px;line-height:1.75;color:#6B6157;margin:0;">
                        Ofrecer a las mujeres colombianas cosméticos e insumos de belleza originales, a precios justos
                        y con un servicio cálido, para que cuidar de sí mismas nunca sea un lujo lejano sino un hábito diario.
                    </p>
                </div>

                {{-- Visión --}}
                <div style="background:linear-gradient(160deg,#FFFFFF 0%,#FBF4E6 60%,#F4E4C5 100%);border:1px solid rgba(217,181,109,0.45);border-radius:18px;padding:clamp(32px,4vw,44px);box-shadow:0 30px 70px -30px rgba(190,154,83,0.32);">
                    <span style="display:block;font-family:'Playfair Display',serif;font-style:italic;font-size:13px;letter-spacing:0.06em;color:#BE9A53;margin-bottom:12px;">Nuestra visión</span>
                    <h3 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:500;color:#2E2A26;margin:0 0 16px;line-height:1.25;">Ser la distribuidora de belleza de confianza del país</h3>
                    <p style="font-size:15px;line-height:1.75;color:#6B6157;margin:0;">
                        Convertirnos en la marca que las colombianas recomiendan con los ojos cerrados, reconocida por su
                        autenticidad, su cercanía y por hacer de cada compra una experiencia bella de principio a fin.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ══════════════ CTA final ══════════════ --}}
    <section style="padding:clamp(56px,8vw,96px) clamp(24px,5vw,48px);">
        <div style="max-width:960px;margin:0 auto;position:relative;overflow:hidden;border-radius:24px;background:linear-gradient(135deg,#EBCF90 0%,#D9B56D 55%,#C4A057 100%);padding:clamp(48px,7vw,80px) clamp(28px,5vw,64px);text-align:center;box-shadow:0 30px 70px -30px rgba(190,154,83,0.6);">
            <div aria-hidden="true" style="position:absolute;top:-80px;right:-60px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,0.18);filter:blur(50px);pointer-events:none;"></div>
            <h2 style="font-family:'Playfair Display',serif;font-size:clamp(26px,3.4vw,42px);font-weight:500;line-height:1.15;color:#3B310F;margin:0 0 16px;position:relative;">
                Descubre productos que realzan tu esencia
            </h2>
            <p style="font-size:clamp(15px,1.6vw,17px);line-height:1.7;color:#5A4A1E;max-width:520px;margin:0 auto 32px;position:relative;">
                Explora nuestro catálogo de cosmética e insumos de belleza originales, con envío a toda Colombia.
            </p>
            <a href="{{ route('products.index') }}"
               style="display:inline-flex;align-items:center;gap:10px;background:#2E2A26;color:#FBF8F2;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;letter-spacing:0.16em;text-transform:uppercase;text-decoration:none;padding:16px 36px;border-radius:999px;box-shadow:0 12px 30px -12px rgba(46,42,38,0.55);transition:transform .3s ease,box-shadow .3s ease;position:relative;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 18px 40px -12px rgba(46,42,38,0.7)'"
               onmouseout="this.style.transform='none';this.style.boxShadow='0 12px 30px -12px rgba(46,42,38,0.55)'">
                Explorar productos
                <svg aria-hidden="true" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </section>

@endsection
