<header id="ba-nav"
        x-data="{ scrolled: false, mobileMenuOpen: false }"
        x-init="
            scrolled = window.scrollY > 24;
            window.addEventListener('scroll', () => { scrolled = window.scrollY > 24; }, { passive: true });
        "
        :class="scrolled ? 'ba-nav--solid' : 'ba-nav--clear'"
        class="ba-nav sticky top-0 z-50 transition-all">
    <style>
        .ba-nav { transition: background-color .35s ease, box-shadow .35s ease, border-color .35s ease; }
        .ba-nav--clear  { background: transparent; border-bottom: 1px solid transparent; box-shadow: none; }
        .ba-nav--solid  { background: rgba(247,243,237,0.92); border-bottom: 1px solid rgba(184,169,153,0.18); box-shadow: 0 1px 16px rgba(46,42,38,0.04); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
    </style>
    <style>
        .ba-bar{position:relative;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;
            height:132px;max-width:1340px;margin:0 auto;padding:0 clamp(54px,6vw,84px);}
        .ba-links{display:flex;align-items:center;gap:clamp(12px,1.5vw,22px);}
        .ba-links--left{justify-content:flex-end;}
        .ba-links--right{justify-content:flex-start;}
        .ba-navlink{font-family:'Montserrat',system-ui,sans-serif;font-size:12.5px;font-weight:500;
            letter-spacing:.11em;text-transform:uppercase;color:#3A352E;text-decoration:none;
            transition:color .3s ease;white-space:nowrap;}
        .ba-navlink:hover{color:#BE9A53;}
        .ba-navlink.is-active{color:#BE9A53;}
        .ba-sep{color:#D9B56D;font-size:10px;line-height:1;opacity:.85;user-select:none;}
        .ba-logo{justify-self:center;display:flex;align-items:center;text-decoration:none;}
        .ba-logo img{width:auto;display:block;}
        .ba-logo .lg{height:126px;} .ba-logo .sm{height:62px;display:none;}
        /* Acciones (cuenta + carrito) agrupadas a la derecha — ya no se enciman */
        .ba-actions{position:absolute;right:clamp(16px,4vw,40px);top:50%;transform:translateY(-50%);
            display:flex;align-items:center;gap:18px;z-index:75;}
        .ba-cart{color:#2E2A26;background:none;border:none;cursor:pointer;transition:color .3s ease;
            padding:10px;min-width:44px;min-height:44px;display:inline-flex;align-items:center;justify-content:center;}
        .ba-cart:hover{color:#BE9A53;}
        .ba-burger{position:absolute;left:clamp(14px,4vw,30px);top:50%;transform:translateY(-50%);
            color:#2E2A26;background:none;border:none;cursor:pointer;padding:10px;min-width:44px;min-height:44px;display:inline-flex;align-items:center;justify-content:center;z-index:80;}
        .ba-burger svg{width:24px;height:24px;}
        @media(max-width:920px){
            .ba-bar{grid-template-columns:1fr;height:88px;padding:0 12px;}
            .ba-links{display:none;}
            .ba-logo .lg{display:block;height:76px;max-width:56vw;} .ba-logo .sm{display:none;}
            .ba-actions{gap:14px;right:12px;}
        }
        @media(min-width:921px){ .ba-burger{display:none;} }
        /* Menú móvil a pantalla completa */
        .ba-mobile-menu{position:fixed;inset:0;z-index:70;background:#F7F3ED;
            display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:32px;}
        .ba-mobile-menu .mm-link{font-family:'Playfair Display',serif;font-size:26px;font-weight:600;
            color:#2E2A26;text-decoration:none;background:none;border:none;cursor:pointer;padding:10px 14px;transition:color .25s;}
        .ba-mobile-menu .mm-link:hover{color:#BE9A53;}
        .ba-mobile-menu .mm-sep{width:44px;height:1px;background:#D9B56D;opacity:.5;margin:6px 0;}
        .ba-mobile-close{position:absolute;top:24px;right:24px;background:none;border:none;color:#8E7E70;
            font-size:30px;line-height:1;cursor:pointer;padding:6px;}
        .ba-mobile-close:hover{color:#2E2A26;}
    </style>
    <nav>
        <div class="ba-bar">
            {{-- Hamburguesa (móvil) --}}
            <button class="ba-burger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menú">
                <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Links izquierda --}}
            <div class="ba-links ba-links--left">
                <a class="ba-navlink {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Inicio</a>
                <span class="ba-sep">&#10022;</span>
                <a class="ba-navlink {{ request()->routeIs('products.*') && !request()->has('type') ? 'is-active' : '' }}" href="{{ route('products.index') }}">Productos</a>
            </div>

            {{-- Logo centrado --}}
            <a href="{{ route('home') }}" class="ba-logo" aria-label="Belleza Áurea — inicio">
                <img class="lg" src="{{ asset('img/logo.png') }}" alt="Belleza Áurea">
                <img class="sm" src="{{ asset('img/isotipo.png') }}" alt="Belleza Áurea">
            </a>

            {{-- Links derecha --}}
            <div class="ba-links ba-links--right">
                <a class="ba-navlink {{ request()->routeIs('blue-light') ? 'is-active' : '' }}" href="{{ route('blue-light') }}">Rituales</a>
                <span class="ba-sep">&#10022;</span>
                <a class="ba-navlink {{ request()->routeIs('landing.quiz*') ? 'is-active' : '' }}" href="{{ route('landing.quiz') }}">Quiz de piel</a>
            </div>

            <div class="ba-actions">
            {{-- Cuenta --}}
            <a href="{{ auth('customer')->check() ? route('account.dashboard') : route('login') }}"
               class="ba-cart relative"
               aria-label="{{ auth('customer')->check() ? 'Mi cuenta' : 'Ingresar' }}"
               title="{{ auth('customer')->check() ? 'Mi cuenta' : 'Ingresar' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                @if(auth('customer')->check())
                <span class="absolute -top-1 -right-1 rounded-full" style="width:9px;height:9px;background:#7C9B7E;border:2px solid #fff;"></span>
                @endif
            </a>

            {{-- Carrito --}}
            <button @click="$dispatch('toggle-cart-drawer')" class="ba-cart relative" id="cart-badge" aria-label="Carrito">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <span id="cart-count"
                      class="absolute -top-2 -right-2 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center transition-transform {{ $cartCount > 0 ? 'scale-100' : 'scale-0' }}"
                      style="background:#D9B56D;">
                    {{ $cartCount }}
                </span>
            </button>
            </div>{{-- /.ba-actions --}}
        </div>

        {{-- Menú móvil — pantalla completa --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="ba-mobile-menu"
             @click.self="mobileMenuOpen = false">
            <button class="ba-mobile-close" @click="mobileMenuOpen = false" aria-label="Cerrar menú">&times;</button>
            <a href="{{ route('home') }}" class="mm-link">Inicio</a>
            <a href="{{ route('products.index') }}" class="mm-link">Productos</a>
            <a href="{{ route('blue-light') }}" class="mm-link">Rituales</a>
            <a href="{{ route('landing.quiz') }}" class="mm-link">Quiz de piel</a>
            <span class="mm-sep"></span>
            <button @click="$dispatch('toggle-cart-drawer'); mobileMenuOpen = false" class="mm-link">Carrito</button>
            @if(auth('customer')->check())
            <a href="{{ route('account.dashboard') }}" class="mm-link">Mi cuenta</a>
            @else
            <a href="{{ route('login') }}" class="mm-link">Ingresar / Registrarme</a>
            @endif
        </div>
    </nav>
</header>

<style>
    @media (max-width: 768px) {
        .nav-brand-text { display: none; }
    }
</style>

{{-- Cart Drawer --}}
<div x-data="cartDrawer()" x-cloak
     @toggle-cart-drawer.window="toggle()"
     @open-cart-drawer.window="open($event.detail)"
     class="fixed inset-0 z-[60]"
     x-show="isOpen">

    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Drawer Panel --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute top-0 right-0 w-full max-w-md max-h-screen shadow-2xl flex flex-col"
         style="background:#ffffff;border-left:1px solid #e5e7eb;border-bottom-left-radius:20px;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 shrink-0" style="border-bottom:1px solid #e5e7eb;">
            <h2 class="font-brand text-lg font-semibold" style="color:#2E2A26;">Tu carrito</h2>
            <button @click="close()" aria-label="Cerrar carrito" class="transition-colors" style="color:#9ca3af;" onmouseover="this.style.color='#2E2A26'" onmouseout="this.style.color='#9ca3af'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Items (lo único que hace scroll) --}}
        <div class="overflow-y-auto px-6 py-4 min-h-0" style="flex:1 1 auto;">
            {{-- Empty state --}}
            <template x-if="items.length === 0">
                <div class="flex flex-col items-center justify-center text-center" style="padding:48px 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" style="color:#d1d5db;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <p class="text-sm" style="color:#9ca3af;">Tu carrito está vacío</p>
                    <a href="{{ route('products.index') }}" class="mt-4 text-sm font-medium transition-colors" style="color:#D9B56D;" onmouseover="this.style.color='#BE9A53'" onmouseout="this.style.color='#D9B56D'">
                        Explorar productos →
                    </a>
                </div>
            </template>

            {{-- Cart items --}}
            <template x-for="item in items" :key="item.key">
                <div class="flex gap-4 py-4" style="border-bottom:1px solid #f3f4f6;">
                    <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0" style="background:#f9fafb;border:1px solid #e5e7eb;">
                        <template x-if="item.image">
                            <img :src="'/storage/' + item.image" :alt="item.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!item.image">
                            <div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#FBF4E6,#E8D1C5);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" style="color:#B8A999;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                                </svg>
                            </div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <a :href="'/productos/' + item.slug" class="text-sm font-medium transition-colors line-clamp-1" style="color:#2E2A26;" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='#2E2A26'" x-text="item.name"></a>
                        <p x-show="item.variant" class="text-xs mt-0.5" style="color:#9ca3af;" x-text="item.variant"></p>
                        <p class="text-sm font-semibold mt-1" style="color:#D9B56D;" x-text="'$' + fmt(item.unit_price)"></p>
                        <div class="flex items-center gap-2 mt-2">
                            <button @click="updateQty(item.key, item.qty - 1)"
                                    class="w-7 h-7 rounded-md flex items-center justify-center text-sm transition-all"
                                    style="background:#f9fafb;border:1px solid #e5e7eb;color:#6b7280;"
                                    onmouseover="this.style.color='#2E2A26';this.style.borderColor='#D9B56D'"
                                    onmouseout="this.style.color='#6b7280';this.style.borderColor='#e5e7eb'">−</button>
                            <span class="text-sm w-6 text-center font-medium" style="color:#2E2A26;" x-text="item.qty"></span>
                            <button @click="updateQty(item.key, item.qty + 1)"
                                    class="w-7 h-7 rounded-md flex items-center justify-center text-sm transition-all"
                                    style="background:#f9fafb;border:1px solid #e5e7eb;color:#6b7280;"
                                    onmouseover="this.style.color='#2E2A26';this.style.borderColor='#D9B56D'"
                                    onmouseout="this.style.color='#6b7280';this.style.borderColor='#e5e7eb'">+</button>
                            <button @click="removeItem(item.key)" aria-label="Quitar producto del carrito" class="ml-auto transition-colors" style="color:#d1d5db;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#d1d5db'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Up-sell dinámico — sugerencias reactivas basadas en el carrito --}}
            @include('partials.cart-upsell')

        </div>

        {{-- Footer: Totals + CTA --}}
        <template x-if="items.length > 0">
            <div class="px-6 py-4 space-y-3 shrink-0" style="border-top:1px solid #e5e7eb;background:#f9fafb;border-bottom-left-radius:20px;">
                {{-- Subtotal --}}
                <div class="flex justify-between text-sm">
                    <span style="color:#6b7280;">Subtotal</span>
                    <span style="color:#2E2A26;" x-text="'$' + fmt(subtotal)"></span>
                </div>

                {{-- Shipping --}}
                <div class="flex justify-between text-sm">
                    <span style="color:#6b7280;">Envío</span>
                    <span x-text="shipping === 0 ? '¡GRATIS!' : '$' + fmt(shipping)"
                          :style="shipping === 0 ? 'color:#16a34a;font-weight:600;' : 'color:#2E2A26;'"></span>
                </div>

                {{-- Shipping progress bar (cuando aun falta para envio gratis basado en TOTAL post-descuentos) --}}
                <template x-if="freeThreshold > 0 && shipping > 0 && (subtotal - coupon_discount) > 0 && (subtotal - coupon_discount) < freeThreshold">
                    <div style="padding:10px 14px;background:#ffffff;border-radius:8px;border:1px solid #e5e7eb;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                            <span style="color:#9ca3af;">Envío gratis</span>
                            <span style="color:#D9B56D;font-weight:500;" x-text="'$' + fmt(freeThreshold - (subtotal - coupon_discount)) + ' más'"></span>
                        </div>
                        <div style="background:#e5e7eb;border-radius:2px;height:4px;overflow:hidden;">
                            <div style="background:#D9B56D;height:100%;border-radius:2px;transition:width .3s ease;"
                                 :style="'width:' + Math.min(((subtotal - coupon_discount) / freeThreshold) * 100, 100) + '%'"></div>
                        </div>
                    </div>
                </template>

                {{-- Free shipping achieved (solo cuando el envio real es 0) --}}
                <template x-if="shipping === 0 && (subtotal - coupon_discount) > 0">
                    <div style="text-align:center;font-size:13px;color:#16a34a;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:8px 14px;">
                        ✓ ¡Envío sin costo!
                    </div>
                </template>

                {{-- Coupon applied --}}
                <template x-if="coupon_code">
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex items-center gap-1.5">
                            <span style="color:#16a34a;">Cupón</span>
                            <span style="font-size:10px;padding:1px 5px;border-radius:3px;background:#f0fdf4;color:#16a34a;font-weight:600;font-family:monospace;" x-text="coupon_code"></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span style="color:#16a34a;" x-text="'-$' + fmt(coupon_discount)"></span>
                            <button @click="removeCoupon()" aria-label="Quitar cupón" style="color:#d1d5db;cursor:pointer;background:none;border:none;padding:0;line-height:1;"
                                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#d1d5db'">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Coupon input --}}
                <template x-if="!coupon_code">
                    <div>
                        <button @click="couponOpen = !couponOpen" type="button"
                                style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:500;color:#D9B56D;background:none;border:none;cursor:pointer;padding:0;font-family:inherit;">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                            </svg>
                            ¿Tienes un cupón?
                        </button>
                        <div x-show="couponOpen" x-collapse x-cloak style="margin-top:8px;">
                            <div style="display:flex;gap:6px;">
                                <input type="text" x-model="couponInput" @keydown.enter.prevent="applyCoupon()"
                                       placeholder="Código"
                                       aria-label="Código de cupón"
                                       style="flex:1;padding:7px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;font-family:inherit;text-transform:uppercase;outline:none;transition:border-color .2s;"
                                       onfocus="this.style.borderColor='#D9B56D'" onblur="this.style.borderColor='#e5e7eb'">
                                <button @click="applyCoupon()" :disabled="couponLoading"
                                        aria-label="Aplicar cupón"
                                        style="padding:7px 12px;background:#2E2A26;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;transition:background .2s;font-family:inherit;"
                                        onmouseover="this.style.background='#D9B56D'" onmouseout="this.style.background='#2E2A26'"
                                        :style="couponLoading ? 'opacity:0.6;cursor:wait;' : ''">
                                    <span x-show="!couponLoading">Aplicar</span>
                                    <span x-show="couponLoading" x-cloak>...</span>
                                </button>
                            </div>
                            <p x-show="couponError" x-cloak x-text="couponError"
                               style="font-size:11px;color:#ef4444;margin-top:4px;"></p>
                        </div>
                    </div>
                </template>

                {{-- Total --}}
                <div class="flex justify-between text-base font-semibold pt-2" style="border-top:1px solid #e5e7eb;">
                    <span style="color:#2E2A26;">Total</span>
                    <span style="color:#D9B56D;" x-text="'$' + fmt(total)"></span>
                </div>

                <a href="{{ route('checkout.index') }}"
                   class="block w-full text-white text-center py-3 rounded-lg font-medium transition-all mt-2"
                   style="background:#D9B56D;box-shadow:0 12px 24px -8px rgba(190,154,83,0.5);"
                   onmouseover="this.style.background='#BE9A53'"
                   onmouseout="this.style.background='#D9B56D'">
                    Finalizar compra
                </a>
                <a href="{{ route('cart.index') }}"
                   class="block w-full text-center text-sm transition-colors py-2"
                   style="color:#6b7280;"
                   onmouseover="this.style.color='#D9B56D'"
                   onmouseout="this.style.color='#6b7280'">
                    Ver carrito completo
                </a>
            </div>
        </template>
    </div>
</div>

<script>
function cartDrawer() {
    return {
        isOpen: false,
        items: @json($cartItemsJson),
        subtotal: {{ $cartSubtotal }},
        discount_2x1: {{ $cartDiscount2x1 }},
        free_items: @json($cartFreeItems),
        coupon_code: @json($cartCouponCode),
        coupon_description: @json($cartCouponDescription),
        coupon_discount: {{ $cartCouponDiscount }},
        shipping: {{ $cartShipping }},
        freeThreshold: {{ $cartFreeThreshold }},
        total: {{ $cartTotal }},
        upsell: @json($upsellSuggestions ?? []),
        couponOpen: true,
        couponInput: '',
        couponLoading: false,
        couponError: '',

        fmt(n) {
            return Math.round(Number(n) || 0).toLocaleString('es-CO');
        },

        open(data) {
            if (data) this.syncFromResponse(data);
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        toggle() {
            this.isOpen ? this.close() : this.open();
        },

        syncFromResponse(data) {
            if (data.items) this.items = data.items;
            if (data.subtotal !== undefined) this.subtotal = data.subtotal;
            if (data.discount_2x1 !== undefined) this.discount_2x1 = data.discount_2x1;
            if (data.free_items !== undefined) this.free_items = data.free_items;
            if (data.coupon_code !== undefined) this.coupon_code = data.coupon_code;
            if (data.coupon_description !== undefined) this.coupon_description = data.coupon_description;
            if (data.coupon_discount !== undefined) this.coupon_discount = data.coupon_discount;
            if (data.shipping !== undefined) this.shipping = data.shipping;
            if (data.free_threshold !== undefined) this.freeThreshold = data.free_threshold;
            if (data.total !== undefined) this.total = data.total;
            if (data.upsell !== undefined) this.upsell = data.upsell;
            this.updateBadge(data.cart_count ?? this.items.reduce((s, i) => s + i.qty, 0));
        },

        updateBadge(count) {
            const badge = document.getElementById('cart-count');
            if (badge) {
                badge.textContent = count;
                badge.classList.toggle('scale-0', count === 0);
                badge.classList.toggle('scale-100', count > 0);
            }
        },

        async updateQty(key, qty) {
            if (qty < 0) return;
            if (qty === 0) return this.removeItem(key);
            try {
                const res = await fetch(`/carrito/actualizar/${key}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ qty }),
                });
                const data = await res.json();
                if (res.ok) this.syncFromResponse(data);
            } catch (e) { console.error('Error updating cart:', e); }
        },

        async removeItem(key) {
            try {
                const res = await fetch(`/carrito/eliminar/${key}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
                const data = await res.json();
                if (res.ok) this.syncFromResponse(data);
            } catch (e) { console.error('Error removing item:', e); }
        },

        async applyCoupon() {
            if (!this.couponInput.trim() || this.couponLoading) return;
            this.couponLoading = true;
            this.couponError = '';
            try {
                const res = await fetch('/checkout/apply-coupon', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ code: this.couponInput.trim() }),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.coupon_code = data.code;
                    this.coupon_description = data.description;
                    this.coupon_discount = data.discount_amount;
                    this.total = data.new_total;
                    this.couponInput = '';
                    this.couponOpen = false;
                } else {
                    this.couponError = data.message || 'Código no válido.';
                }
            } catch (e) {
                this.couponError = 'Error al aplicar el cupón.';
            } finally {
                this.couponLoading = false;
            }
        },

        async removeCoupon() {
            try {
                const res = await fetch('/checkout/remove-coupon', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
                const data = await res.json();
                if (res.ok) {
                    this.coupon_code = null;
                    this.coupon_description = null;
                    this.coupon_discount = 0;
                    this.total = data.new_total;
                    if (data.upsell !== undefined) this.upsell = data.upsell;
                }
            } catch (e) { console.error('Error removing coupon:', e); }
        },

        /**
         * Añade al carrito una sugerencia del up-sell.
         * Se hace en un fetch al mismo endpoint que /carrito/agregar y se
         * dispara una animación de "vuelo" del thumb al icono del carrito.
         */
        async addFromUpsell(p, evt) {
            if (p._loading) return;
            // Marca reactiva del ítem específico (usar splice/replace para Alpine).
            const idx = this.upsell.findIndex(u => u.id === p.id);
            if (idx > -1) this.upsell[idx] = { ...this.upsell[idx], _loading: true };

            // Animación "vuelo": clonamos la imagen y la desplazamos hacia el icono del carrito.
            try { this.flyToCart(evt); } catch (_) { /* animación opcional */ }

            try {
                const res = await fetch('/carrito/agregar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ product_id: p.id, qty: 1 }),
                });
                const data = await res.json();
                if (res.ok) this.syncFromResponse(data);
            } catch (e) {
                console.error('Error añadiendo upsell:', e);
            } finally {
                const j = this.upsell.findIndex(u => u.id === p.id);
                if (j > -1) this.upsell[j] = { ...this.upsell[j], _loading: false };
            }
        },

        /**
         * Anima el thumbnail del producto hacia el icono del carrito.
         * Puramente cosmética — si algo falla no rompe el flujo.
         */
        flyToCart(evt) {
            const card = evt?.target?.closest('div[style*="border-radius:14px"]');
            const thumbEl = card?.querySelector('img');
            const target = document.getElementById('cart-badge');
            if (!thumbEl || !target) return;

            const from = thumbEl.getBoundingClientRect();
            const to = target.getBoundingClientRect();

            const ghost = thumbEl.cloneNode(true);
            Object.assign(ghost.style, {
                position: 'fixed',
                left: from.left + 'px',
                top: from.top + 'px',
                width: from.width + 'px',
                height: from.height + 'px',
                borderRadius: '12px',
                objectFit: 'contain',
                pointerEvents: 'none',
                zIndex: '9999',
                transition: 'transform .7s cubic-bezier(.4,0,.2,1), opacity .7s ease',
                background: '#fff',
                boxShadow: '0 10px 24px -8px rgba(190,154,83,.5)',
            });
            document.body.appendChild(ghost);

            const dx = (to.left + to.width / 2) - (from.left + from.width / 2);
            const dy = (to.top + to.height / 2) - (from.top + from.height / 2);

            requestAnimationFrame(() => {
                ghost.style.transform = `translate(${dx}px, ${dy}px) scale(0.2)`;
                ghost.style.opacity = '0.2';
            });
            setTimeout(() => ghost.remove(), 720);
        },
    };
}
</script>
