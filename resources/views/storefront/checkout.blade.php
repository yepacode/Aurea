@extends('layouts.app')

@section('title', 'Checkout | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')

    <style>
        /* ===== Checkout — estética Belleza Áurea ===== */
        .checkout-scope,
        .checkout-scope input, .checkout-scope select,
        .checkout-scope textarea, .checkout-scope button{
            font-family:'Montserrat', system-ui, sans-serif;
        }
        .checkout-scope label{
            font-size:.72rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:#8A8073;
        }
        /* Las tarjetas de método de pago no llevan el estilo de label de campo */
        .checkout-scope label.cursor-pointer{
            font-size:inherit;font-weight:inherit;letter-spacing:normal;text-transform:none;color:inherit;
        }
        .co-card{
            position:relative;
            background:#FFFFFF;
            border:1px solid rgba(217,181,109,0.22);
            border-radius:22px;
            padding:26px;
            box-shadow:0 20px 48px -32px rgba(46,42,38,0.55);
            overflow:hidden;
        }
        @media(min-width:768px){ .co-card{padding:32px;} }
        .co-card::after{
            content:"";position:absolute;top:12px;right:12px;width:44px;height:44px;
            background:url('/img/patterns/leaf-corner.svg') no-repeat center/contain;
            opacity:.45;pointer-events:none;
        }
        .co-summary{
            background:linear-gradient(180deg,#FCFAF5 0%,#F6EFE1 100%);
            border:1px solid rgba(217,181,109,0.32);
        }
        .co-head{
            font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:#2E2A26;
            display:flex;align-items:center;gap:12px;margin-bottom:22px;line-height:1.2;
        }
        .co-head__ic{
            width:38px;height:38px;border-radius:50%;flex-shrink:0;
            background:radial-gradient(circle at 35% 28%, #F5E7C6, #E4C384);
            display:flex;align-items:center;justify-content:center;color:#8A6E2E;
            box-shadow:0 0 0 4px rgba(217,181,109,0.14);
        }
        .co-head__ic svg{width:19px;height:19px;}
        /* Inputs con foco dorado (sobre el estilo base del compañero) */
        .checkout-scope input:not([type=radio]):not([type=checkbox]),
        .checkout-scope select,
        .checkout-scope textarea{
            background:#FCFBF8 !important;
            border:1px solid rgba(46,42,38,0.14) !important;
            border-radius:12px !important;
            transition:border-color .2s, box-shadow .2s, background .2s !important;
        }
        .checkout-scope input:not([type=radio]):not([type=checkbox]):focus,
        .checkout-scope select:focus,
        .checkout-scope textarea:focus{
            border-color:#D9B56D !important;
            box-shadow:0 0 0 3px rgba(217,181,109,0.20) !important;
            background:#FFFFFF !important;
        }
        .checkout-scope label{ color:#6B6157; }
        /* Método de pago seleccionado en dorado */
        .checkout-scope .co-pay--on{
            border-color:#D9B56D !important;
            background:rgba(217,181,109,0.08) !important;
            box-shadow:0 6px 18px -12px rgba(190,154,83,0.6);
        }
        /* Botón confirmar — esbelto y elegante */
        .co-submit{
            width:100%;margin-top:22px;
            display:flex;align-items:center;justify-content:center;gap:8px;
            padding:13px 20px;border-radius:9999px;
            font-family:'Montserrat',sans-serif;font-size:.82rem;font-weight:600;letter-spacing:.03em;
            color:#3B310F;
            background:linear-gradient(135deg,#EBCF90 0%,#D9B56D 55%,#C4A057 100%);
            box-shadow:0 10px 24px -13px rgba(190,154,83,0.85);
            transition:transform .2s, box-shadow .2s, filter .2s;
        }
        .co-submit:hover:not(:disabled){ transform:translateY(-1px); box-shadow:0 15px 30px -13px rgba(190,154,83,0.95); filter:brightness(1.03); }
        .co-submit:disabled{ opacity:.55; cursor:not-allowed; }
        .co-submit svg{ width:16px;height:16px; }
    </style>

    {{-- Page header --}}
    <section class="relative overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" style="padding-top:clamp(32px,5vh,60px);padding-bottom:clamp(12px,2vh,24px);">
            <nav class="flex items-center justify-center gap-2 text-sm mb-4" style="color:#B8A999;">
                <a href="{{ route('home') }}" style="color:#B8A999;text-decoration:none;" onmouseover="this.style.color='#BE9A53'" onmouseout="this.style.color='#B8A999'">Inicio</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                <a href="{{ route('cart.index') }}" style="color:#B8A999;text-decoration:none;" onmouseover="this.style.color='#BE9A53'" onmouseout="this.style.color='#B8A999'">Carrito</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                <span style="color:#8E7E70;">Checkout</span>
            </nav>
            <h1 class="font-brand font-semibold" style="font-family:'Playfair Display',serif;color:#2E2A26;font-size:clamp(30px,4vw,46px);">Finalizar compra</h1>
        </div>
    </section>

    @include('partials.ba-divider')

    {{-- Checkout form --}}
    <section class="checkout-scope" style="padding-top:clamp(32px,5vw,56px);padding-bottom:clamp(80px,10vw,128px);">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"
             x-data="checkoutForm()" x-cloak>

            <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

                {{-- LEFT COLUMN: Customer data + Payment --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Shipping Info --}}
                    <div class="co-card">
                        <h2 class="co-head">
                            <span class="co-head__ic">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            </span>
                            Datos de envío
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Name --}}
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-text-muted mb-1.5">Nombre completo *</label>
                                <input type="text" id="name" x-model="form.name"
                                       class="w-full border border-border-light rounded-xl px-4 py-3 text-sm text-text-dark placeholder-text-muted/40 focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors"
                                       placeholder="Tu nombre completo">
                                <p x-show="errors.name" x-text="errors.name" class="text-danger text-xs mt-1"></p>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-text-muted mb-1.5">Email *</label>
                                <input type="email" id="email" x-model="form.email"
                                       class="w-full border border-border-light rounded-xl px-4 py-3 text-sm text-text-dark placeholder-text-muted/40 focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors"
                                       placeholder="tu@email.com">
                                <p x-show="errors.email" x-text="errors.email" class="text-danger text-xs mt-1"></p>
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-text-muted mb-1.5">Teléfono</label>
                                <input type="tel" id="phone" x-model="form.phone"
                                       class="w-full border border-border-light rounded-xl px-4 py-3 text-sm text-text-dark placeholder-text-muted/40 focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors"
                                       placeholder="10 dígitos">
                            </div>

                            {{-- Address --}}
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-sm font-medium text-text-muted mb-1.5">Dirección *</label>
                                <input type="text" id="address" x-model="form.address"
                                       class="w-full border border-border-light rounded-xl px-4 py-3 text-sm text-text-dark placeholder-text-muted/40 focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors"
                                       placeholder="Calle, número, barrio">
                                <p x-show="errors.address" x-text="errors.address" class="text-danger text-xs mt-1"></p>
                            </div>

                            {{-- Departamento --}}
                            <div>
                                <label for="state" class="block text-sm font-medium text-text-muted mb-1.5">Departamento *</label>
                                <select id="state" x-model="form.state"
                                        class="w-full border border-border-light rounded-xl px-4 py-3 text-sm text-text-dark focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors"
                                        :class="!form.state ? 'text-text-muted/40' : ''">
                                    <option value="" disabled>Selecciona un departamento</option>
                                    <option value="Amazonas">Amazonas</option>
                                    <option value="Antioquia">Antioquia</option>
                                    <option value="Arauca">Arauca</option>
                                    <option value="Atlántico">Atlántico</option>
                                    <option value="Bogotá D.C.">Bogotá D.C.</option>
                                    <option value="Bolívar">Bolívar</option>
                                    <option value="Boyacá">Boyacá</option>
                                    <option value="Caldas">Caldas</option>
                                    <option value="Caquetá">Caquetá</option>
                                    <option value="Casanare">Casanare</option>
                                    <option value="Cauca">Cauca</option>
                                    <option value="Cesar">Cesar</option>
                                    <option value="Chocó">Chocó</option>
                                    <option value="Córdoba">Córdoba</option>
                                    <option value="Cundinamarca">Cundinamarca</option>
                                    <option value="Guainía">Guainía</option>
                                    <option value="Guaviare">Guaviare</option>
                                    <option value="Huila">Huila</option>
                                    <option value="La Guajira">La Guajira</option>
                                    <option value="Magdalena">Magdalena</option>
                                    <option value="Meta">Meta</option>
                                    <option value="Nariño">Nariño</option>
                                    <option value="Norte de Santander">Norte de Santander</option>
                                    <option value="Putumayo">Putumayo</option>
                                    <option value="Quindío">Quindío</option>
                                    <option value="Risaralda">Risaralda</option>
                                    <option value="San Andrés y Providencia">San Andrés y Providencia</option>
                                    <option value="Santander">Santander</option>
                                    <option value="Sucre">Sucre</option>
                                    <option value="Tolima">Tolima</option>
                                    <option value="Valle del Cauca">Valle del Cauca</option>
                                    <option value="Vaupés">Vaupés</option>
                                    <option value="Vichada">Vichada</option>
                                </select>
                                <p x-show="errors.state" x-text="errors.state" class="text-danger text-xs mt-1"></p>
                            </div>

                            {{-- Zip code --}}
                            <div>
                                <label for="zip_code" class="block text-sm font-medium text-text-muted mb-1.5">Código postal *</label>
                                <input type="text" id="zip_code" x-model="form.zip_code"
                                       class="w-full border border-border-light rounded-xl px-4 py-3 text-sm text-text-dark placeholder-text-muted/40 focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors"
                                       placeholder="00000" maxlength="10">
                                <p x-show="errors.zip_code" x-text="errors.zip_code" class="text-danger text-xs mt-1"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="co-card">
                        <h2 class="co-head">
                            <span class="co-head__ic">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                            </span>
                            Método de pago
                        </h2>

                        <div class="space-y-3">
                            {{-- Card --}}
                            <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-all duration-200"
                                   :class="form.payment_method === 'card' ? 'co-pay--on' : 'border-border-light hover:border-primary/30'">
                                <input type="radio" x-model="form.payment_method" value="card" class="text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <span class="text-sm font-semibold text-text-dark">Tarjeta de crédito / débito</span>
                                    <p class="text-xs text-text-muted mt-0.5">Pago seguro con Stripe</p>
                                </div>
                                <svg class="w-8 h-5" style="color:#BE9A53;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                            </label>

                            {{-- ePayco (PSE, tarjetas, Nequi, efectivo) --}}
                            <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-all duration-200"
                                   :class="form.payment_method === 'epayco' ? 'co-pay--on' : 'border-border-light hover:border-primary/30'">
                                <input type="radio" x-model="form.payment_method" value="epayco" class="text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <span class="text-sm font-semibold text-text-dark">PSE, tarjetas, Nequi y efectivo</span>
                                    <p class="text-xs text-text-muted mt-0.5">Pago seguro con ePayco</p>
                                </div>
                                <svg class="w-5 h-5" style="color:#BE9A53;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
                            </label>

                            {{-- Transfer --}}
                            <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-all duration-200"
                                   :class="form.payment_method === 'transfer' ? 'co-pay--on' : 'border-border-light hover:border-primary/30'">
                                <input type="radio" x-model="form.payment_method" value="transfer" class="text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <span class="text-sm font-semibold text-text-dark">Transferencia bancaria</span>
                                    <p class="text-xs text-text-muted mt-0.5">Recibirás los datos bancarios por correo</p>
                                </div>
                                <svg class="w-5 h-5" style="color:#BE9A53;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21"/></svg>
                            </label>

                        </div>

                        {{-- Stripe Card Element --}}
                        <div x-show="form.payment_method === 'card'" x-transition class="mt-6">
                            <label class="block text-sm font-medium text-text-muted mb-2">Datos de la tarjeta</label>
                            <div id="card-element" class="border border-border-light rounded-xl px-4 py-3.5 bg-white focus-within:border-secondary/50 focus-within:ring-2 focus-within:ring-secondary/10 transition-colors"></div>
                            <p x-show="cardError" x-text="cardError" class="text-danger text-xs mt-2"></p>
                            <div class="mt-3 flex items-center gap-2 text-xs text-text-muted/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                Pago seguro encriptado con Stripe
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="co-card">
                        <label for="notes" class="block text-sm font-medium text-text-muted mb-1.5">Notas adicionales (opcional)</label>
                        <textarea id="notes" x-model="form.notes" rows="3"
                                  class="w-full border border-border-light rounded-xl px-4 py-3 text-sm text-text-dark placeholder-text-muted/40 focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors resize-none"
                                  placeholder="Instrucciones especiales de entrega, referencias..."></textarea>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="co-card co-summary sticky top-24">
                        <h2 class="co-head" style="margin-bottom:18px;">
                            <span class="co-head__ic">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                            </span>
                            Resumen del pedido
                        </h2>

                        {{-- 2x1 hint: odd number of eligible lenses --}}
                        @if($eligibleLensCount > 0 && $eligibleLensCount % 2 !== 0)
                        <div class="mb-4 flex items-center gap-2 px-2.5 py-2 rounded-lg bg-blue-50/70 border border-blue-100">
                            <svg class="w-3.5 h-3.5 text-secondary/70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                            </svg>
                            <p class="text-[11px] text-text-muted leading-tight">Llevas 2×1 — <a href="{{ route('products.index') }}" class="text-secondary hover:underline">escoge otro</a> y va gratis</p>
                        </div>
                        @endif

                        {{-- 2x1 applied --}}
                        @if($discount2x1 > 0)
                        <div class="mb-4 flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-green-50/70 border border-green-100">
                            <svg class="w-3 h-3 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            <p class="text-[11px] text-green-700 leading-tight">2×1 aplicado — {{ implode(', ', $freeItems) }} gratis</p>
                        </div>
                        @endif

                        {{-- Cart items --}}
                        <div class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            @foreach($items as $item)
                            <div class="flex gap-3 pb-4 border-b border-border-light last:border-0 last:pb-0">
                                @php $img = $item['product']->images[0] ?? null; @endphp
                                <div class="w-16 h-16 rounded-lg bg-bg-light border border-border-light flex-shrink-0 overflow-hidden flex items-center justify-center">
                                    @if($img)
                                    <img src="{{ asset('storage/' . $img) }}" alt="{{ $item['product']->name }}" class="w-full h-full object-contain p-1">
                                    @else
                                    <span class="text-text-muted/30 text-xs">{{ Str::limit($item['product']->name, 10) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-text-dark truncate">{{ $item['product']->name }}</p>
                                    @if($item['variant'])
                                        @if($item['variant']->value)
                                        <p class="text-xs text-text-muted/60">{{ $item['variant']->value }}</p>
                                        @endif
                                        @if($item['variant']->graduation)
                                        @php
                                            $gradTypeLabels = ['miopia' => 'Miopía', 'lectura' => 'Lectura', 'sin_graduacion' => 'Sin graduación'];
                                            $gradTypeLabel = $gradTypeLabels[$item['variant']->graduation_type] ?? null;
                                        @endphp
                                        <p class="text-xs text-text-muted/60">
                                            Graduación: <span class="font-medium text-text-dark">{{ $item['variant']->graduation }}</span>@if($gradTypeLabel) <span class="text-text-muted/50">({{ $gradTypeLabel }})</span>@endif
                                        </p>
                                        @endif
                                    @endif
                                    <p class="text-xs text-text-muted/60">Cant: {{ $item['qty'] }}</p>
                                </div>
                                <p class="text-sm font-semibold text-text-dark">${{ number_format($item['total'], 0, ',', '.') }}</p>
                            </div>
                            @endforeach
                        </div>

                        {{-- Coupon code --}}
                        <div class="mt-6 pt-4 border-t border-border-light">
                            <template x-if="!coupon.code">
                                <div>
                                    <label class="block text-sm font-medium text-text-muted mb-2">Código de descuento</label>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="couponInput" placeholder="Ej: AUREA10"
                                               style="text-transform: uppercase"
                                               @keydown.enter.prevent="applyCoupon()"
                                               class="flex-1 border border-border-light rounded-lg px-3 py-2 text-sm text-text-dark placeholder-text-muted/40 focus:outline-none focus:border-secondary/50 focus:ring-2 focus:ring-secondary/10 transition-colors">
                                        <button type="button" @click="applyCoupon()" :disabled="applyingCoupon || !couponInput.trim()"
                                                class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span x-show="!applyingCoupon">Aplicar</span>
                                            <span x-show="applyingCoupon" class="flex items-center">
                                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                    <p x-show="couponError" x-text="couponError" class="text-danger text-xs mt-1.5"></p>
                                </div>
                            </template>
                            <template x-if="coupon.code">
                                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2.5">
                                    <div>
                                        <span class="text-sm font-mono font-semibold text-green-800" x-text="coupon.code"></span>
                                        <p class="text-xs text-green-600" x-text="coupon.description"></p>
                                    </div>
                                    <button type="button" @click="removeCoupon()" class="text-green-600 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Totals --}}
                        <div class="mt-4 pt-4 border-t border-border-light space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-text-muted">Subtotal</span>
                                <span class="text-text-dark">${{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($discount2x1 > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Descuento 2×1</span>
                                <span>-${{ number_format($discount2x1, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-text-muted">Envío</span>
                                <span class="text-text-dark"
                                      x-text="currentShipping > 0 ? money(currentShipping) : 'Gratis'"></span>
                            </div>
                            <template x-if="coupon.discount_amount > 0">
                                <div class="flex justify-between text-green-600">
                                    <span>Cupón</span>
                                    <span x-text="'-' + money(coupon.discount_amount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between font-bold text-lg pt-3 border-t border-border-light">
                                <span class="text-text-dark">Total</span>
                                <span class="text-primary" x-text="money(currentTotal)"></span>
                            </div>
                        </div>

                        {{-- Submit button --}}
                        <button type="submit" :disabled="processing" class="co-submit">
                            <span x-show="!processing" class="flex items-center justify-center gap-2">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                <span>Confirmar pedido</span>
                            </span>
                            <span x-show="processing" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </button>

                        {{-- General error --}}
                        <p x-show="generalError" x-text="generalError" class="text-danger text-sm mt-3 text-center"></p>

                        {{-- Trust badges — confianza en el pago --}}
                        <div class="mt-5 pt-4 border-t border-border-light flex flex-wrap justify-center gap-x-4 gap-y-2.5 text-[11px] text-text-muted/80">
                            <span class="inline-flex items-center gap-1.5">
                                <svg aria-hidden="true" class="w-3.5 h-3.5 flex-shrink-0" style="color:#BE9A53;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                Pago 100% seguro
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg aria-hidden="true" class="w-3.5 h-3.5 flex-shrink-0" style="color:#BE9A53;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                Datos protegidos (Ley 1581)
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg aria-hidden="true" class="w-3.5 h-3.5 flex-shrink-0" style="color:#BE9A53;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-6.75m0 0V6.375c0-.621-.504-1.125-1.125-1.125H4.5m0 0h-.375A1.125 1.125 0 0 0 3 6.375v6.75m1.5-9V4.5m0 0h9.75c.621 0 1.125.504 1.125 1.125V9"/></svg>
                                Envío a toda Colombia
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg aria-hidden="true" class="w-3.5 h-3.5 flex-shrink-0" style="color:#BE9A53;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Compra garantizada
                            </span>
                        </div>

                        {{-- Benefits --}}
                        @php
                            $benefits = !empty($productBenefits) ? $productBenefits : [
                                'Pago seguro',
                                'Envío gratis +$' . number_format($freeThreshold ?? 999, 0),
                            ];
                        @endphp
                        <div class="mt-5 pt-4 border-t border-border-light grid grid-cols-2 gap-2 text-xs text-text-muted/70">
                            @foreach($benefits as $benefit)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                                {{ $benefit }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@push('scripts')
<script>
function checkoutForm() {
    return {
        form: {
            name: @js($prefill['name'] ?? ''),
            email: @js($prefill['email'] ?? ''),
            phone: @js($prefill['phone'] ?? ''),
            address: @js($prefill['address'] ?? ''),
            state: @js($prefill['state'] ?? ''),
            zip_code: @js($prefill['zip_code'] ?? ''),
            payment_method: 'epayco',
            stripe_payment_intent_id: '',
            notes: '',
        },
        errors: {},
        cardError: '',
        generalError: '',
        processing: false,
        stripe: null,
        cardElement: null,
        cardMounted: false,
        couponInput: '',
        couponError: '',
        applyingCoupon: false,
        coupon: {
            code: {!! json_encode($appliedCoupon) !!},
            description: {!! json_encode($discount['amount'] > 0 ? ($discount['code'] ? 'Descuento aplicado' : '') : '') !!},
            discount_amount: {{ $discount['amount'] ?? 0 }},
        },
        currentShipping: {{ $shipping }},
        currentTotal: {{ $total }},

        money(v) {
            return '$' + Math.round(Number(v) || 0).toLocaleString('es-CO');
        },

        init() {
            this.stripe = Stripe('{{ config("services.stripe.key") }}');
            const elements = this.stripe.elements();
            this.cardElement = elements.create('card', {
                style: {
                    base: {
                        fontFamily: '"Montserrat", sans-serif',
                        fontSize: '15px',
                        color: '#2E2A26',
                        '::placeholder': { color: '#B8AC9A' },
                    },
                    invalid: { color: '#C0392B' },
                },
                hidePostalCode: true,
            });

            this.$watch('form.payment_method', (val) => {
                if (val === 'card') {
                    this.$nextTick(() => this.mountCard());
                }
            });

            // Recalculate shipping when state changes
            this.$watch('form.state', (val) => {
                if (val) this.recalculateShipping();
            });

            this.$nextTick(() => {
                if (this.form.payment_method === 'card') {
                    this.mountCard();
                }
            });
        },

        async recalculateShipping() {
            try {
                const res = await fetch('{{ route("checkout.calculateShipping") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ state: this.form.state }),
                });
                const data = await res.json();
                if (res.ok) {
                    this.currentShipping = data.shipping;
                    this.currentTotal = data.total;
                }
            } catch (e) {
                console.error('Error recalculating shipping:', e);
            }
        },

        mountCard() {
            const el = document.getElementById('card-element');
            if (el && !this.cardMounted) {
                this.cardElement.mount('#card-element');
                this.cardMounted = true;
                this.cardElement.on('change', (event) => {
                    this.cardError = event.error ? event.error.message : '';
                });
            }
        },

        async applyCoupon() {
            this.couponError = '';
            if (!this.couponInput.trim()) return;
            this.applyingCoupon = true;

            try {
                const res = await fetch('{{ route("checkout.applyCoupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: this.couponInput.trim() }),
                });

                const data = await res.json();

                if (!res.ok) {
                    this.couponError = data.message || 'Código no válido.';
                    this.applyingCoupon = false;
                    return;
                }

                this.coupon = {
                    code: data.code,
                    description: data.description,
                    discount_amount: data.discount_amount,
                };
                if (typeof data.shipping !== 'undefined') {
                    this.currentShipping = data.shipping;
                }
                this.currentTotal = data.new_total;
                this.couponInput = '';
            } catch (e) {
                this.couponError = 'Error al aplicar el código.';
            }
            this.applyingCoupon = false;
        },

        async removeCoupon() {
            try {
                const res = await fetch('{{ route("checkout.removeCoupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await res.json();
                if (res.ok) {
                    this.coupon = { code: null, description: '', discount_amount: 0 };
                    if (typeof data.shipping !== 'undefined') {
                        this.currentShipping = data.shipping;
                    }
                    this.currentTotal = data.new_total;
                }
            } catch (e) {
                console.error('Error removing coupon:', e);
            }
        },

        async handleSubmit() {
            this.processing = true;
            this.errors = {};
            this.cardError = '';
            this.generalError = '';

            if (!this.validate()) {
                this.processing = false;
                return;
            }

            if (this.form.payment_method === 'card') {
                await this.handleCardPayment();
            } else {
                await this.submitOrder();
            }
        },

        validate() {
            const required = { name: 'Nombre', email: 'Email', address: 'Dirección', state: 'Estado', zip_code: 'Código postal' };
            for (const [field, label] of Object.entries(required)) {
                if (!this.form[field]?.trim()) {
                    this.errors[field] = `${label} es requerido.`;
                }
            }
            if (this.form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
                this.errors.email = 'Email inválido.';
            }
            if (Object.keys(this.errors).length > 0) {
                const firstError = document.querySelector('[x-show="errors.' + Object.keys(this.errors)[0] + '"]');
                if (firstError) firstError.closest('div')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return Object.keys(this.errors).length === 0;
        },

        async handleCardPayment() {
            try {
                const res = await fetch('{{ route("checkout.createPaymentIntent") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ state: this.form.state }),
                });

                if (!res.ok) {
                    const data = await res.json();
                    this.generalError = data.message || 'Error al preparar el pago.';
                    this.processing = false;
                    return;
                }

                const { clientSecret } = await res.json();

                const { error, paymentIntent } = await this.stripe.confirmCardPayment(
                    clientSecret,
                    { payment_method: { card: this.cardElement } }
                );

                if (error) {
                    this.cardError = error.message;
                    this.processing = false;
                    return;
                }

                if (paymentIntent.status === 'succeeded' || paymentIntent.status === 'processing') {
                    this.form.stripe_payment_intent_id = paymentIntent.id;
                    await this.submitOrder();
                } else {
                    this.generalError = 'El pago no fue completado. Intenta de nuevo.';
                    this.processing = false;
                }
            } catch (e) {
                this.generalError = 'Error inesperado. Intenta de nuevo.';
                this.processing = false;
            }
        },

        async submitOrder() {
            try {
                const res = await fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form),
                });

                if (res.status === 422) {
                    const data = await res.json();
                    this.errors = data.errors || {};
                    this.generalError = data.message || '';
                    this.processing = false;
                    return;
                }

                const data = await res.json();
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                this.generalError = 'Error al crear el pedido.';
                this.processing = false;
            } catch (e) {
                this.generalError = 'Error al crear el pedido. Intenta de nuevo.';
                this.processing = false;
            }
        },
    };
}
</script>
@endpush
