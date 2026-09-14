@extends('layouts.app')

@section('title', $seoSettings->meta_title ?? 'Envíos y devoluciones | Belleza Áurea')
@section('meta_description', $seoSettings->meta_description ?? 'Información sobre envíos, devoluciones y garantía de Belleza Áurea. Conoce nuestros tiempos de entrega y políticas.')
@section('canonical', $seoSettings->canonical_url ?? route('shipping-returns'))
@section('og_title', $seoSettings->og_title ?? $seoSettings->meta_title ?? 'Envíos y devoluciones | Belleza Áurea')
@section('og_description', $seoSettings->og_description ?? $seoSettings->meta_description ?? 'Tiempos de entrega y políticas de Belleza Áurea.')
@section('twitter_title', $seoSettings->twitter_title ?? $seoSettings->meta_title ?? 'Envíos y devoluciones | Belleza Áurea')
@section('twitter_description', $seoSettings->twitter_description ?? $seoSettings->meta_description ?? 'Tiempos de entrega y políticas de Belleza Áurea.')
@section('og_image', ($seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))
@section('twitter_image', ($seoSettings->twitter_image_url ?? $seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))

@section('content')

    {{-- Hero con identidad de marca (ramitas doradas + rocío) --}}
    @include('partials.legal-hero', [
        'title'    => $page->hero_title ?? 'Envíos y devoluciones',
        'subtitle' => $page->hero_subtitle ?? 'Todo lo que necesitas saber sobre nuestros tiempos de entrega y políticas de devolución.',
        'current'  => 'Envíos y devoluciones',
    ])

    @include('partials.ba-divider')

    {{-- Content --}}
    <section style="padding-top:clamp(40px,6vw,72px);padding-bottom:clamp(24px,3vw,40px);">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

            {{-- Envíos --}}
            <div class="bg-white rounded-2xl p-6 md:p-8 border border-border-light shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                        </svg>
                    </div>
                    <h2 class="font-brand text-xl font-bold text-text-dark">
                        {{ $page->shipping_title ?? 'Política de envíos' }}
                    </h2>
                </div>
                <div class="prose prose-sm text-text-muted leading-relaxed max-w-none">
                    {!! \App\Support\SafeHtml::sanitize($page->shipping_content ?? '<p>Realizamos envíos a todo el territorio nacional.</p>
                    <ul>
                        <li>El costo de envío y el monto para <strong>envío gratis</strong> se calculan automáticamente en el carrito según tu zona.</li>
                        <li><strong>Tiempo de entrega:</strong> 2 a 6 días hábiles según tu ubicación.</li>
                        <li>Recibirás la guía para rastrear tu pedido en cuanto sea despachado.</li>
                    </ul>') !!}
                </div>
            </div>

            {{-- Devoluciones --}}
            <div class="bg-white rounded-2xl p-6 md:p-8 border border-border-light shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/>
                        </svg>
                    </div>
                    <h2 class="font-brand text-xl font-bold text-text-dark">
                        {{ $page->returns_title ?? 'Política de devoluciones' }}
                    </h2>
                </div>
                <div class="prose prose-sm text-text-muted leading-relaxed max-w-none">
                    {!! \App\Support\SafeHtml::sanitize($page->returns_content ?? '<p>Cuentas con el <strong>derecho de retracto</strong>: tienes 5 días hábiles desde que recibes tu pedido para solicitar la devolución.</p>
                    <ul>
                        <li>El producto debe estar sin usar y en su empaque original.</li>
                        <li>Por higiene, algunos productos de uso personal no admiten devolución una vez abiertos.</li>
                        <li>Escríbenos con tu número de pedido y te guiamos en todo el proceso.</li>
                        <li>El reembolso se procesa una vez recibamos y revisemos el producto.</li>
                    </ul>') !!}
                </div>
            </div>

            {{-- Garantía --}}
            <div class="bg-white rounded-2xl p-6 md:p-8 border border-border-light shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                        </svg>
                    </div>
                    <h2 class="font-brand text-xl font-bold text-text-dark">
                        {{ $page->warranty_title ?? 'Garantía' }}
                    </h2>
                </div>
                <div class="prose prose-sm text-text-muted leading-relaxed max-w-none">
                    {!! \App\Support\SafeHtml::sanitize($page->warranty_content ?? '<p>Todos nuestros productos cuentan con la <strong>garantía legal</strong> por defectos de calidad o idoneidad.</p>
                    <ul>
                        <li>Cubre defectos de fabricación del producto.</li>
                        <li>No cubre daños por mal uso o manipulación inadecuada.</li>
                        <li>Para hacerla efectiva, escríbenos con fotos y tu número de pedido.</li>
                    </ul>') !!}
                </div>
            </div>

        </div>
    </section>

    {{-- CTA --}}
    <section style="padding-top:8px;padding-bottom:clamp(72px,9vw,128px);">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="font-brand text-2xl font-bold" style="font-family:'Playfair Display',serif;color:#2E2A26;">¿Tienes más dudas?</h2>
            <p class="mt-3" style="color:#6B6157;">Estamos para ayudarte.</p>
            <a href="{{ route('contact') }}"
               class="inline-block mt-6 px-8 py-3 rounded-full font-semibold text-sm transition-all duration-200"
               style="background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);color:#3B310F;box-shadow:0 10px 24px -13px rgba(190,154,83,0.85);"
               onmouseover="this.style.filter='brightness(1.04)'" onmouseout="this.style.filter='none'">
                Contáctanos
            </a>
        </div>
    </section>

@endsection
