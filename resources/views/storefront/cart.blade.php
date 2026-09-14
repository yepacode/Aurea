@extends('layouts.app')

@section('title', 'Carrito de compras | Belleza Áurea')
@section('robots', 'noindex, follow')

@section('content')
<section class="py-12 flex-1" style="background:#FBF8F2;" x-data="cartPage()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <h1 class="font-brand text-3xl font-bold" style="color:#2E2A26;">Tu carrito</h1>
            <a href="{{ route('products.index') }}" class="text-sm font-medium transition-colors" style="color:#D9B56D;" onmouseover="this.style.color='#BE9A53'" onmouseout="this.style.color='#D9B56D'">
                ← Seguir comprando
            </a>
        </div>

        {{-- Empty state --}}
        <template x-if="items.length === 0">
            <div class="text-center py-24" style="background:#ffffff;border-radius:16px;border:1px solid #e5e7eb;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto mb-6" style="color:#d1d5db;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <h2 class="text-xl font-semibold mb-2" style="color:#2E2A26;">Tu carrito está vacío</h2>
                <p class="mb-6" style="color:#9ca3af;">Agrega productos para comenzar tu compra</p>
                <a href="{{ route('products.index') }}" class="inline-block text-white px-8 py-3 rounded-lg font-medium transition-colors" style="background:#D9B56D;" onmouseover="this.style.background='#BE9A53'" onmouseout="this.style.background='#D9B56D'">
                    Explorar productos
                </a>
            </div>
        </template>

        {{-- Cart with items --}}
        <template x-if="items.length > 0">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Items list (2/3) --}}
                <div class="lg:col-span-2 space-y-1">
                    {{-- Table header (desktop) --}}
                    <div class="hidden md:grid grid-cols-12 gap-4 px-4 py-3 text-xs uppercase tracking-wider" style="color:#9ca3af;border-bottom:1px solid #e5e7eb;">
                        <div class="col-span-6">Producto</div>
                        <div class="col-span-2 text-center">Precio</div>
                        <div class="col-span-2 text-center">Cantidad</div>
                        <div class="col-span-2 text-right">Total</div>
                    </div>

                    {{-- Items --}}
                    <template x-for="item in items" :key="item.key">
                        <div class="grid grid-cols-12 gap-4 items-center px-4 py-5 group rounded-lg transition-colors" style="border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#ffffff'" onmouseout="this.style.background='transparent'">
                            <div class="col-span-12 md:col-span-6 flex gap-4">
                                <div class="w-20 h-20 md:w-24 md:h-24 rounded-xl overflow-hidden flex-shrink-0" style="background:#ffffff;border:1px solid #e5e7eb;">
                                    <template x-if="item.image">
                                        <img :src="'/storage/' + item.image" :alt="item.name" style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;padding:6px;">
                                    </template>
                                    <template x-if="!item.image">
                                        <div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#FBF4E6,#E8D1C5);">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" style="color:#B8A999;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex flex-col justify-center min-w-0">
                                    <a :href="'/productos/' + item.slug" class="text-sm font-medium line-clamp-2 transition-colors" style="color:#2E2A26;" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='#2E2A26'" x-text="item.name"></a>
                                    <p x-show="item.variant" class="text-xs mt-1" style="color:#9ca3af;" x-text="item.variant"></p>
                                    <p class="md:hidden text-sm font-semibold mt-1" style="color:#D9B56D;" x-text="'$' + fmt(item.unit_price)"></p>
                                </div>
                            </div>
                            <div class="hidden md:flex col-span-2 justify-center">
                                <span class="text-sm" style="color:#2E2A26;" x-text="'$' + fmt(item.unit_price)"></span>
                            </div>
                            <div class="col-span-8 md:col-span-2 flex justify-start md:justify-center">
                                <div class="flex items-center rounded-lg overflow-hidden" style="border:1px solid #e5e7eb;">
                                    <button @click="updateQty(item.key, item.qty - 1)" class="w-9 h-9 flex items-center justify-center text-sm transition-all" style="color:#6b7280;" onmouseover="this.style.color='#2E2A26';this.style.background='#f3f4f6'" onmouseout="this.style.color='#6b7280';this.style.background='transparent'">−</button>
                                    <span class="w-10 h-9 flex items-center justify-center text-sm font-medium" style="color:#2E2A26;border-left:1px solid #e5e7eb;border-right:1px solid #e5e7eb;background:#f9fafb;" x-text="item.qty"></span>
                                    <button @click="updateQty(item.key, item.qty + 1)" class="w-9 h-9 flex items-center justify-center text-sm transition-all" style="color:#6b7280;" onmouseover="this.style.color='#2E2A26';this.style.background='#f3f4f6'" onmouseout="this.style.color='#6b7280';this.style.background='transparent'">+</button>
                                </div>
                            </div>
                            <div class="col-span-4 md:col-span-2 flex items-center justify-end gap-3">
                                <span class="text-sm font-semibold" style="color:#2E2A26;" x-text="'$' + fmt(item.total)"></span>
                                <button @click="removeItem(item.key)" class="transition-colors" style="color:#d1d5db;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#d1d5db'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                </div>

                {{-- Summary sidebar (1/3) --}}
                <div class="lg:col-span-1">
                    <div class="sticky rounded-2xl p-6 space-y-4" style="top:150px;background:linear-gradient(160deg,#FEFCF8,#F8F2E8);border:1px solid rgba(217,181,109,.18);box-shadow:0 30px 60px -34px rgba(120,92,44,.4);">
                        <h2 class="font-brand text-lg font-semibold" style="color:#2E2A26;">Resumen del pedido</h2>

                        <div class="space-y-3 text-sm">
                            {{-- Subtotal --}}
                            <div class="flex justify-between">
                                <span style="color:#6b7280;">Subtotal</span>
                                <span style="color:#2E2A26;" x-text="'$' + fmt(subtotal)"></span>
                            </div>

                            {{-- Shipping --}}
                            <div class="flex justify-between">
                                <span style="color:#6b7280;">Envío</span>
                                <span x-text="shipping === 0 ? '¡GRATIS!' : '$' + fmt(shipping)"
                                      :style="shipping === 0 ? 'color:#16a34a;font-weight:600;' : 'color:#2E2A26;'"></span>
                            </div>

                            {{-- Progreso para envio gratis (basado en TOTAL post-descuentos) --}}
                            <template x-if="freeThreshold > 0 && shipping > 0 && (subtotal - coupon_discount) > 0 && (subtotal - coupon_discount) < freeThreshold">
                                <div style="padding:10px 14px;background:#f9fafb;border-radius:8px;border:1px solid #f3f4f6;">
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

                            {{-- Envio gratis conseguido (solo cuando shipping real es 0) --}}
                            <template x-if="shipping === 0 && (subtotal - coupon_discount) > 0">
                                <div style="text-align:center;font-size:13px;color:#16a34a;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:8px 14px;">
                                    ✓ ¡Envío sin costo!
                                </div>
                            </template>

                            {{-- Coupon discount applied --}}
                            <template x-if="coupon_code">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-1.5">
                                        <span style="color:#16a34a;">Cupón</span>
                                        <span style="font-size:11px;padding:1px 6px;border-radius:4px;background:#f0fdf4;color:#16a34a;font-weight:600;font-family:monospace;" x-text="coupon_code"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span style="color:#16a34a;" x-text="'-$' + fmt(coupon_discount)"></span>
                                        <button @click="removeCoupon()" style="color:#d1d5db;cursor:pointer;background:none;border:none;padding:0;line-height:1;"
                                                onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#d1d5db'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Coupon input --}}
                        <template x-if="!coupon_code">
                            <div style="border-top:1px solid #e5e7eb;padding-top:16px;">
                                <button @click="couponOpen = !couponOpen" type="button"
                                        style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#D9B56D;background:none;border:none;cursor:pointer;padding:0;font-family:inherit;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                                    </svg>
                                    ¿Tienes un cupón de descuento?
                                </button>
                                <div x-show="couponOpen" x-collapse x-cloak style="margin-top:10px;">
                                    <div style="display:flex;gap:8px;">
                                        <input type="text" x-model="couponInput" @keydown.enter.prevent="applyCoupon()"
                                               placeholder="Código de cupón"
                                               style="flex:1;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:inherit;text-transform:uppercase;outline:none;transition:border-color .2s;"
                                               onfocus="this.style.borderColor='#D9B56D'" onblur="this.style.borderColor='#e5e7eb'">
                                        <button @click="applyCoupon()" :disabled="couponLoading"
                                                style="padding:8px 16px;background:#2E2A26;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background .2s;font-family:inherit;white-space:nowrap;"
                                                onmouseover="this.style.background='#D9B56D'" onmouseout="this.style.background='#2E2A26'"
                                                :style="couponLoading ? 'opacity:0.6;cursor:wait;' : ''">
                                            <span x-show="!couponLoading">Aplicar</span>
                                            <span x-show="couponLoading" x-cloak>...</span>
                                        </button>
                                    </div>
                                    <p x-show="couponError" x-cloak x-text="couponError"
                                       style="font-size:12px;color:#ef4444;margin-top:6px;"></p>
                                    <p x-show="couponSuccess" x-cloak x-text="couponSuccess"
                                       style="font-size:12px;color:#16a34a;margin-top:6px;"></p>
                                </div>
                            </div>
                        </template>

                        {{-- Total --}}
                        <div class="pt-4 flex justify-between items-center" style="border-top:1px solid #e5e7eb;">
                            <span class="text-base font-semibold" style="color:#2E2A26;">Total</span>
                            <span class="text-xl font-bold" style="color:#D9B56D;" x-text="'$' + fmt(total)"></span>
                        </div>

                        <a href="{{ route('checkout.index') }}"
                           class="block w-full text-white text-center py-4 font-semibold transition-all"
                           style="background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);border-radius:999px;letter-spacing:.12em;text-transform:uppercase;font-size:13px;box-shadow:0 16px 32px -12px rgba(190,154,83,.65);"
                           onmouseover="this.style.boxShadow='0 22px 44px -12px rgba(190,154,83,.85)';this.style.transform='translateY(-2px)'"
                           onmouseout="this.style.boxShadow='0 16px 32px -12px rgba(190,154,83,.65)';this.style.transform='translateY(0)'">
                            Finalizar compra
                        </a>

                        {{-- Beneficios (misma fuente que /checkout: editable en /admin/pages/lentes) --}}
                        @php
                            $cartBenefits = !empty($productBenefits) ? $productBenefits : [
                                ($freeThreshold ?? 0) > 0 ? 'Envío gratis desde $' . number_format($freeThreshold, 0, ',', '.') : 'Envío a toda Colombia',
                                'Productos originales',
                                'Pago 100% seguro',
                                'Soporte por WhatsApp',
                            ];
                        @endphp
                        <div class="mt-1 pt-3 border-t grid grid-cols-2 gap-2 text-xs" style="border-color:#e5e7eb;color:#9ca3af;">
                            @foreach($cartBenefits as $benefit)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                                {{ $benefit }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</section>
@endsection

@push('scripts')
<script>
function cartPage() {
    return {
        items: @json($items),
        subtotal: {{ $subtotal }},
        discount_2x1: {{ $discount_2x1 }},
        free_items: @json($free_items),
        coupon_code: @json($coupon_code ?? null),
        coupon_description: @json($coupon_description ?? null),
        coupon_discount: {{ $coupon_discount ?? 0 }},
        shipping: {{ $shipping }},
        freeThreshold: {{ $free_threshold ?? 999 }},
        total: {{ $total }},
        couponOpen: false,
        couponInput: '',
        couponLoading: false,
        couponError: '',
        couponSuccess: '',

        fmt(n) {
            return Math.round(Number(n) || 0).toLocaleString('es-CO');
        },

        updateBadge(count) {
            const badge = document.getElementById('cart-count');
            if (badge) {
                badge.textContent = count;
                badge.classList.toggle('scale-0', count === 0);
                badge.classList.toggle('scale-100', count > 0);
            }
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
            this.updateBadge(data.cart_count ?? this.items.reduce((s, i) => s + i.qty, 0));
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
            this.couponSuccess = '';

            try {
                const res = await fetch('{{ route("checkout.applyCoupon") }}', {
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
                    this.couponSuccess = data.description + ' aplicado';
                    this.couponInput = '';
                    this.couponOpen = false;
                } else {
                    this.couponError = data.message || 'Código no válido.';
                }
            } catch (e) {
                this.couponError = 'Error al aplicar el cupón.';
                console.error('Error applying coupon:', e);
            } finally {
                this.couponLoading = false;
            }
        },

        async removeCoupon() {
            try {
                const res = await fetch('{{ route("checkout.removeCoupon") }}', {
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
                    this.couponError = '';
                    this.couponSuccess = '';
                }
            } catch (e) { console.error('Error removing coupon:', e); }
        },
    };
}
</script>
@endpush
