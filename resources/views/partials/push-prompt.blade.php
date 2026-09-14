{{-- ═══════════════════════════════════════════════════════════
     Belleza Áurea — Prompt de suscripción a notificaciones push
     ───────────────────────────────────────────────────────────
     • Aparece a los 30s (o al scroll significativo, lo primero).
     • No se muestra si el navegador no soporta push.
     • No se muestra si el usuario ya decidió (permission != 'default').
     • Si dice "ahora no", no vuelve a preguntar por 30 días.
     • Oculto en /admin, /checkout, /ingresar, /carrito, /cuenta y /pedido.
     ═══════════════════════════════════════════════════════════ --}}
@php
    $__pushPath = '/' . ltrim(request()->path(), '/');
    $__pushHide = collect(['/admin', '/checkout', '/ingresar', '/carrito', '/cuenta', '/pedido', '/epayco'])
        ->contains(fn ($p) => str_starts_with($__pushPath, $p));
@endphp
@unless($__pushHide)
<div id="ba-push-prompt" role="dialog" aria-labelledby="ba-push-title" aria-hidden="true" hidden>
    <div class="ba-push__card">
        <button type="button" class="ba-push__x" aria-label="Cerrar" data-push-action="dismiss">×</button>
        <div class="ba-push__icon" aria-hidden="true">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-4-5.66V5a2 2 0 1 0-4 0v.34A6 6 0 0 0 6 11v3.2c0 .53-.21 1.04-.59 1.42L4 17h5"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 17a3 3 0 0 0 6 0" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="ba-push__text">
            <h3 id="ba-push-title" class="ba-push__title">💛 Belleza Áurea</h3>
            <p class="ba-push__body">¿Te avisamos cuando bajen los precios y lleguen tus favoritos? Sin spam, prometido.</p>
        </div>
        <div class="ba-push__actions">
            <button type="button" class="ba-push__btn ba-push__btn--primary" data-push-action="accept">Sí, quiero</button>
            <button type="button" class="ba-push__btn ba-push__btn--ghost"   data-push-action="dismiss">Ahora no</button>
        </div>
    </div>
</div>

<style>
    #ba-push-prompt{
        position:fixed;left:clamp(12px,3vw,24px);bottom:clamp(12px,3vw,24px);
        z-index:9500;max-width:360px;width:calc(100% - 24px);
        opacity:0;transform:translateY(16px);pointer-events:none;
        transition:opacity .35s ease,transform .35s ease;
        font-family:'Montserrat',system-ui,sans-serif;
    }
    #ba-push-prompt.is-open{opacity:1;transform:translateY(0);pointer-events:auto;}
    .ba-push__card{
        position:relative;padding:20px 22px 18px;border-radius:22px;
        background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);
        border:1px solid rgba(217,181,109,.28);
        box-shadow:0 30px 60px -30px rgba(120,92,44,.45),0 4px 14px -8px rgba(0,0,0,.08);
        color:#2E2A26;
    }
    .ba-push__x{
        position:absolute;top:8px;right:10px;width:28px;height:28px;border-radius:999px;
        border:none;background:transparent;color:#7a6e5d;font-size:22px;line-height:1;cursor:pointer;
    }
    .ba-push__x:hover{background:rgba(46,42,38,.06);color:#2E2A26;}
    .ba-push__icon{
        width:44px;height:44px;border-radius:999px;display:flex;align-items:center;justify-content:center;
        background:radial-gradient(circle at 30% 30%,#F5E1B0 0%,#D9B56D 78%);color:#2E2A26;margin-bottom:10px;
    }
    .ba-push__title{
        font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:#2E2A26;margin:0 0 4px;letter-spacing:.01em;
    }
    .ba-push__body{
        font-size:13.5px;line-height:1.5;color:#5b5348;margin:0 0 14px;
    }
    .ba-push__actions{display:flex;gap:8px;flex-wrap:wrap;}
    .ba-push__btn{
        flex:1;min-width:120px;padding:11px 14px;border-radius:999px;font-size:12.5px;font-weight:600;
        letter-spacing:.04em;text-transform:uppercase;cursor:pointer;transition:all .2s ease;border:1px solid transparent;
    }
    .ba-push__btn--primary{
        background:linear-gradient(135deg,#D9B56D 0%,#BE9A53 100%);color:#fff;
        box-shadow:0 8px 20px -10px rgba(190,154,83,.7);
    }
    .ba-push__btn--primary:hover{transform:translateY(-1px);box-shadow:0 14px 26px -12px rgba(190,154,83,.85);}
    .ba-push__btn--ghost{background:transparent;color:#7a6e5d;border-color:rgba(122,110,93,.25);}
    .ba-push__btn--ghost:hover{background:rgba(122,110,93,.06);color:#2E2A26;}
    @media (max-width:400px){
        .ba-push__title{font-size:16px;}
        .ba-push__body{font-size:12.5px;}
    }
    @media (prefers-reduced-motion:reduce){
        #ba-push-prompt{transition:opacity .01s;}
    }
</style>

<script>
(function(){
    'use strict';

    // ── 0. Guards de soporte ─────────────────────────────
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) return;
    if (typeof Notification === 'undefined') return;
    if (Notification.permission !== 'default') return;

    // Si el usuario dijo "ahora no" hace < 30 días, no volvemos a preguntar.
    var DISMISS_KEY = 'ba_push_dismissed_at';
    try {
        var last = parseInt(localStorage.getItem(DISMISS_KEY) || '0', 10);
        if (last && (Date.now() - last) < 30 * 24 * 3600 * 1000) return;
    } catch(e) {}

    var el = document.getElementById('ba-push-prompt');
    if (!el) return;

    // ── 1. Mostrar tras 30s o al scroll significativo (lo que ocurra antes) ──
    var shown = false;
    function show() {
        if (shown) return; shown = true;
        el.hidden = false;
        // fuerza reflow para que la transición corra
        requestAnimationFrame(function(){ el.classList.add('is-open'); el.setAttribute('aria-hidden','false'); });
    }
    setTimeout(show, 30000);
    var scrollThreshold = Math.max(600, window.innerHeight * 1.5);
    window.addEventListener('scroll', function onScroll(){
        if (window.scrollY > scrollThreshold) {
            show();
            window.removeEventListener('scroll', onScroll);
        }
    }, { passive: true });

    // ── 2. Cerrar (sin pedir permiso) ─────────────────────
    function hide() {
        el.classList.remove('is-open');
        el.setAttribute('aria-hidden','true');
        setTimeout(function(){ el.hidden = true; }, 400);
    }
    function dismiss() {
        try { localStorage.setItem(DISMISS_KEY, String(Date.now())); } catch(e) {}
        hide();
    }

    // ── 3. Aceptar → registrar SW push y suscribir ───────
    function urlBase64ToUint8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var raw = atob(base64);
        var arr = new Uint8Array(raw.length);
        for (var i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
        return arr;
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    async function subscribeFlow() {
        try {
            var perm = await Notification.requestPermission();
            if (perm !== 'granted') { dismiss(); return; }

            var keyResp = await fetch('{{ url('/push/public-key') }}', { credentials: 'same-origin' });
            var keyJson = await keyResp.json();
            if (!keyJson.key) { alert('Aún no hay VAPID configurado.'); hide(); return; }

            var reg = await navigator.serviceWorker.register('{{ asset('sw-push.js') }}');
            await navigator.serviceWorker.ready;

            var sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(keyJson.key),
            });

            var json = sub.toJSON();
            await fetch('{{ url('/push/subscribe') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    endpoint:   sub.endpoint,
                    public_key: json.keys.p256dh,
                    auth_token: json.keys.auth,
                }),
            });

            hide();
        } catch (e) {
            console.warn('Push subscribe error', e);
            hide();
        }
    }

    el.addEventListener('click', function(ev){
        var t = ev.target.closest('[data-push-action]');
        if (!t) return;
        var action = t.getAttribute('data-push-action');
        if (action === 'accept') subscribeFlow();
        else if (action === 'dismiss') dismiss();
    });
})();
</script>
@endunless
