{{--
    Cart drawer up-sell.
    ------------------------------------------------------------------
    Se renderiza dentro del drawer cuando hay items en el carrito.
    Se apoya en el scope de Alpine `cartDrawer()`: usa `upsell`,
    `freeThreshold`, `subtotal`, `coupon_discount` y `addFromUpsell()`.
    El array `upsell` se pobla en cada respuesta JSON de los endpoints
    /carrito/* y /checkout/{apply,remove}-coupon (ver CartController).
--}}
<template x-if="items.length > 0 && upsell && upsell.length > 0">
    <div style="padding:14px 0 6px;border-top:1px dashed rgba(217,181,109,.35);margin-top:6px;">
        {{-- Título dinámico: si falta para envío gratis se muestra el CTA con progreso,
             si no se muestra la sugerencia complementaria. --}}
        <template x-if="freeThreshold > 0 && (subtotal - coupon_discount) < freeThreshold">
            <div style="margin:0 0 12px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin:0 0 6px;">
                    <span style="font-size:13px;font-weight:600;color:#2E2A26;letter-spacing:.01em;">
                        🎁 Añade y desbloquea envío gratis
                    </span>
                    <span style="font-size:12px;color:#BE9A53;font-weight:600;"
                          x-text="'$' + fmt(Math.max(0, freeThreshold - (subtotal - coupon_discount))) + ' faltan'"></span>
                </div>
                <div style="background:#F3ECDF;border-radius:9999px;height:6px;overflow:hidden;">
                    <div style="height:100%;border-radius:9999px;background:linear-gradient(90deg,#EBCF90,#D9B56D 55%,#BE9A53);transition:width .5s cubic-bezier(.2,.7,.3,1);"
                         :style="'width:' + Math.min(((subtotal - coupon_discount) / freeThreshold) * 100, 100) + '%'"></div>
                </div>
            </div>
        </template>

        <template x-if="!(freeThreshold > 0 && (subtotal - coupon_discount) < freeThreshold)">
            <p style="font-size:12px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#BE9A53;margin:0 0 10px;">
                💛 Podrías querer también
            </p>
        </template>

        {{-- Tarjetas horizontales compactas --}}
        <div style="display:flex;flex-direction:column;gap:10px;">
            <template x-for="p in upsell" :key="'upsell-' + p.id">
                <div style="display:flex;gap:12px;align-items:center;padding:10px;background:#FBF8F2;border:1px solid rgba(217,181,109,.22);border-radius:14px;transition:border-color .25s ease, box-shadow .25s ease;"
                     onmouseover="this.style.borderColor='rgba(217,181,109,.55)';this.style.boxShadow='0 10px 22px -14px rgba(190,154,83,.45)'"
                     onmouseout="this.style.borderColor='rgba(217,181,109,.22)';this.style.boxShadow='none'">
                    {{-- Imagen 60x60 --}}
                    <a :href="'/productos/' + p.slug" @click="close()"
                       style="flex-shrink:0;width:60px;height:60px;border-radius:12px;overflow:hidden;background:linear-gradient(155deg,#FFFDF9,#F3ECDF);display:flex;align-items:center;justify-content:center;">
                        <template x-if="p.image">
                            <img :src="'/storage/' + p.image" :alt="p.name" loading="lazy" style="max-width:100%;max-height:100%;object-fit:contain;">
                        </template>
                        <template x-if="!p.image">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color:#B8A999;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                            </svg>
                        </template>
                    </a>

                    {{-- Nombre + precio --}}
                    <div style="flex:1;min-width:0;">
                        <a :href="'/productos/' + p.slug" @click="close()"
                           class="line-clamp-1"
                           style="display:block;font-size:13px;font-weight:600;color:#2E2A26;text-decoration:none;line-height:1.3;transition:color .2s;"
                           onmouseover="this.style.color='#BE9A53'" onmouseout="this.style.color='#2E2A26'"
                           x-text="p.name"></a>
                        <p style="font-family:'Playfair Display',serif;font-size:14px;font-weight:600;color:#BE9A53;margin:2px 0 0;"
                           x-text="p.price_fmt"></p>
                    </div>

                    {{-- Botón "+" circular --}}
                    <button type="button"
                            @click="addFromUpsell(p, $event)"
                            :disabled="p._loading"
                            :aria-label="'Añadir ' + p.name + ' al carrito'"
                            :title="'Añadir ' + p.name + ' al carrito'"
                            style="flex-shrink:0;width:38px;height:38px;border-radius:50%;border:none;cursor:pointer;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#BE9A53);color:#3B310F;font-size:22px;font-weight:400;line-height:1;display:flex;align-items:center;justify-content:center;transition:transform .25s ease, box-shadow .25s ease, filter .2s ease;box-shadow:0 8px 18px -10px rgba(190,154,83,.55);"
                            onmouseover="this.style.transform='scale(1.08) rotate(90deg)';this.style.boxShadow='0 12px 22px -10px rgba(190,154,83,.75)'"
                            onmouseout="this.style.transform='none';this.style.boxShadow='0 8px 18px -10px rgba(190,154,83,.55)'"
                            :style="p._loading ? 'opacity:0.6;cursor:wait;' : ''">
                        <span x-show="!p._loading" style="display:inline-block;margin-top:-2px;">+</span>
                        <svg x-show="p._loading" x-cloak class="animate-spin" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"></circle>
                            <path fill="currentColor" opacity=".9" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>
</template>
