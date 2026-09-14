{{-- =========================================================================
     Widget WhatsApp — Belleza Áurea
     Estados: colapsado (FAB con badge online/offline) y expandido (popup).
     La detección online/offline se hace 100% en PHP (America/Bogota),
     el JS solo maneja abrir/cerrar y persistencia por sesión.
     ========================================================================= --}}
@php
    $__wa       = \App\Models\ContactPageSetting::getCurrent();
    $__waOn     = $__wa->whatsapp_widget_enabled ?? true;
@endphp

@if($__waOn)
    @php
        $__waOnline   = $__wa->isWhatsappOnline();
        $__waNumber   = $__wa->whatsappDigits();
        $__waMsg      = $__wa->whatsappPredefinedMessage();
        $__waUrl      = \App\Models\ContactPageSetting::whatsappUrl();
        $__waWelcome  = $__waOnline ? $__wa->whatsappWelcomeOnline() : $__wa->whatsappWelcomeOffline();
        $__waHoursTxt = $__wa->whatsappHoursSummary();
    @endphp

<style>
    /* ── FAB (colapsado) ─────────────────────────────────────────────── */
    .ba-wa{position:fixed;bottom:20px;right:20px;z-index:9998;width:84px;height:84px;display:block;
        background:transparent;border:0;padding:0;cursor:pointer;text-decoration:none;
        animation:ba-wa-float 5s ease-in-out infinite;}
    @keyframes ba-wa-float{0%,100%{transform:translateY(0);}50%{transform:translateY(-5px);}}
    .ba-wa__svg{width:100%;height:100%;overflow:visible;filter:drop-shadow(0 9px 14px rgba(46,42,38,.32));transition:transform .35s cubic-bezier(.2,.8,.3,1);}
    .ba-wa:hover .ba-wa__svg{transform:scale(1.08) rotate(-2deg);}
    .ba-wa .wa-spk{transform-box:fill-box;transform-origin:center;animation:ba-wa-tw 2.6s ease-in-out infinite;}
    .ba-wa .wa-spk.s2{animation-delay:.7s;}
    .ba-wa .wa-spk.s3{animation-delay:1.4s;}
    .ba-wa .wa-spk.s4{animation-delay:2s;}
    @keyframes ba-wa-tw{0%,100%{opacity:.15;transform:scale(.6);}50%{opacity:1;transform:scale(1);}}

    /* Badge online/offline en la esquina del FAB */
    .ba-wa__badge{position:absolute;top:2px;right:2px;display:inline-flex;align-items:center;gap:5px;
        padding:3px 8px 3px 6px;border-radius:9999px;
        font:600 10px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.06em;text-transform:uppercase;
        background:#FBF7EE;color:#2E2A26;border:1px solid rgba(46,42,38,.12);
        box-shadow:0 4px 12px rgba(46,42,38,.18);white-space:nowrap;}
    .ba-wa__badge::before{content:"";width:7px;height:7px;border-radius:50%;background:#9AA79A;box-shadow:0 0 0 2px rgba(154,167,154,.25);}
    .ba-wa[data-online="true"] .ba-wa__badge::before{background:#25D366;box-shadow:0 0 0 2px rgba(37,211,102,.28);animation:ba-wa-pulse 1.8s ease-in-out infinite;}
    @keyframes ba-wa-pulse{0%,100%{box-shadow:0 0 0 2px rgba(37,211,102,.28);}50%{box-shadow:0 0 0 6px rgba(37,211,102,0);}}

    /* ── Popup (expandido) ───────────────────────────────────────────── */
    .ba-wa-pop{position:fixed;bottom:118px;right:20px;z-index:9999;width:min(340px,calc(100vw - 32px));
        background:linear-gradient(180deg,#FEFCF8 0%,#F8F2E8 100%);
        border:1px solid rgba(217,181,109,.28);border-radius:22px;
        box-shadow:0 30px 70px -30px rgba(46,42,38,.5),0 8px 24px -12px rgba(0,0,0,.12);
        overflow:hidden;transform-origin:bottom right;
        opacity:0;pointer-events:none;transform:translateY(12px) scale(.96);
        transition:opacity .28s ease,transform .28s cubic-bezier(.2,.8,.3,1);}
    .ba-wa-pop.is-open{opacity:1;pointer-events:auto;transform:translateY(0) scale(1);}
    .ba-wa-pop__hd{display:flex;align-items:center;gap:12px;padding:16px 18px;
        background:linear-gradient(120deg,#2E2A26 0%,#3B342C 100%);color:#FBF7EE;}
    .ba-wa-pop__hd-logo{width:38px;height:38px;border-radius:9999px;
        background:radial-gradient(circle at 30% 30%,#EBD298,#BE9A53 70%,#8C6E38);
        display:flex;align-items:center;justify-content:center;font:700 18px 'Playfair Display',serif;color:#2E2A26;
        box-shadow:inset 0 0 0 1px rgba(255,255,255,.35);}
    .ba-wa-pop__hd-title{font:600 15px/1.15 'Playfair Display',serif;letter-spacing:.02em;}
    .ba-wa-pop__hd-sub{margin-top:3px;display:inline-flex;align-items:center;gap:6px;
        font:500 11px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase;color:rgba(251,247,238,.75);}
    .ba-wa-pop__hd-sub::before{content:"";width:7px;height:7px;border-radius:50%;background:#9AA79A;box-shadow:0 0 0 2px rgba(154,167,154,.25);}
    .ba-wa-pop[data-online="true"] .ba-wa-pop__hd-sub::before{background:#25D366;box-shadow:0 0 0 2px rgba(37,211,102,.3);}
    .ba-wa-pop__close{margin-left:auto;background:transparent;border:0;color:rgba(251,247,238,.7);cursor:pointer;
        width:30px;height:30px;border-radius:9999px;display:flex;align-items:center;justify-content:center;
        transition:background .2s ease,color .2s ease;}
    .ba-wa-pop__close:hover{background:rgba(251,247,238,.1);color:#FBF7EE;}
    .ba-wa-pop__body{padding:18px;}
    .ba-wa-pop__bubble{background:#fff;border:1px solid rgba(46,42,38,.08);border-radius:14px 14px 14px 4px;
        padding:12px 14px;font:400 14px/1.45 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        box-shadow:0 4px 12px -4px rgba(46,42,38,.08);}
    .ba-wa-pop__cta{display:flex;align-items:center;justify-content:center;gap:10px;
        margin-top:14px;padding:13px 18px;width:100%;
        background:#25D366;color:#fff;border-radius:12px;text-decoration:none;
        font:600 14px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.02em;
        box-shadow:0 8px 22px -6px rgba(37,211,102,.55);
        transition:background .2s ease,transform .2s ease,box-shadow .2s ease;}
    .ba-wa-pop__cta:hover{background:#1FBD5A;transform:translateY(-1px);box-shadow:0 12px 26px -8px rgba(37,211,102,.65);}
    .ba-wa-pop__cta svg{width:18px;height:18px;flex-shrink:0;}
    .ba-wa-pop__hours{margin-top:12px;padding:10px 12px;border-radius:10px;
        background:rgba(217,181,109,.1);border:1px solid rgba(217,181,109,.22);
        font:400 11.5px/1.4 'Montserrat',system-ui,sans-serif;color:#5A4B33;}
    .ba-wa-pop__hours strong{color:#8C6E38;font-weight:600;letter-spacing:.04em;text-transform:uppercase;font-size:10.5px;}
    .ba-wa-pop__ft{padding:10px 18px;border-top:1px solid rgba(46,42,38,.06);
        font:400 11px/1.3 'Montserrat',system-ui,sans-serif;color:#8A7A5F;text-align:center;letter-spacing:.03em;}

    @media (prefers-reduced-motion: reduce){
        .ba-wa,.ba-wa .wa-spk,.ba-wa[data-online="true"] .ba-wa__badge::before,
        .ba-wa-pop[data-online="true"] .ba-wa-pop__hd-sub::before{animation:none;}
        .ba-wa-pop{transition:opacity .15s ease;}
    }
    @media (max-width:480px){
        .ba-wa{width:68px;height:68px;bottom:14px;right:14px;}
        .ba-wa__badge{top:0;right:-2px;font-size:9px;padding:2px 7px 2px 5px;}
        .ba-wa-pop{bottom:96px;right:14px;}
    }
</style>

{{-- Popup expandido --}}
<div id="ba-wa-pop" class="ba-wa-pop" role="dialog" aria-modal="false" aria-label="Chat de WhatsApp Belleza Áurea"
     data-online="{{ $__waOnline ? 'true' : 'false' }}" hidden>
    <div class="ba-wa-pop__hd">
        <div class="ba-wa-pop__hd-logo" aria-hidden="true">A</div>
        <div>
            <div class="ba-wa-pop__hd-title">Belleza Áurea</div>
            <div class="ba-wa-pop__hd-sub">{{ $__waOnline ? 'En línea' : 'Fuera de horario' }}</div>
        </div>
        <button type="button" class="ba-wa-pop__close" data-ba-wa-close aria-label="Cerrar chat">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <div class="ba-wa-pop__body">
        <div class="ba-wa-pop__bubble">{{ $__waWelcome }}</div>
        <a class="ba-wa-pop__cta" href="{{ $__waUrl }}" target="_blank" rel="noopener"
           data-ba-wa-cta aria-label="Escribir por WhatsApp">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2zm5.46 12.38c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51-.17-.01-.37-.01-.57-.01-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.19 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z"/></svg>
            Escribir por WhatsApp
        </a>
        <div class="ba-wa-pop__hours">
            <strong>Horario de atención</strong><br>
            {{ $__waHoursTxt }}
        </div>
    </div>
</div>

{{-- FAB colapsado (idéntico al histórico + badge de estado) --}}
<button type="button" id="ba-wa-fab" class="ba-wa" data-online="{{ $__waOnline ? 'true' : 'false' }}"
        aria-label="Abrir chat de WhatsApp" aria-expanded="false" aria-controls="ba-wa-pop">
    <span class="ba-wa__badge">{{ $__waOnline ? 'En línea' : 'Offline' }}</span>
    <svg class="ba-wa__svg" viewBox="0 0 84 84" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="waGreen" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#AFB99C"/><stop offset=".55" stop-color="#8A9A73"/><stop offset="1" stop-color="#6E7C55"/>
            </linearGradient>
            <linearGradient id="waGold" x1="0" y1="1" x2="1" y2="0">
                <stop offset="0" stop-color="#BE9A53"/><stop offset="1" stop-color="#EBD298"/>
            </linearGradient>
        </defs>
        <g stroke="url(#waGold)" stroke-width="1.5" fill="none" stroke-linecap="round">
            <path d="M42 79 C30 77 20 67 16 51 C14.6 45 14 39.5 14.6 34"/>
            <path d="M42 79 C54 77 64 67 68 51 C69.4 45 70 39.5 69.4 34"/>
        </g>
        <g fill="url(#waGold)">
            <ellipse cx="34" cy="75.5" rx="4.2" ry="1.7" transform="rotate(26 34 75.5)"/>
            <ellipse cx="24.5" cy="68.5" rx="4.4" ry="1.8" transform="rotate(48 24.5 68.5)"/>
            <ellipse cx="18.5" cy="58.5" rx="4.2" ry="1.7" transform="rotate(68 18.5 58.5)"/>
            <ellipse cx="15.6" cy="47.5" rx="3.7" ry="1.55" transform="rotate(83 15.6 47.5)"/>
            <ellipse cx="14.7" cy="37.5" rx="3.1" ry="1.4" transform="rotate(92 14.7 37.5)"/>
            <ellipse cx="50" cy="75.5" rx="4.2" ry="1.7" transform="rotate(-26 50 75.5)"/>
            <ellipse cx="59.5" cy="68.5" rx="4.4" ry="1.8" transform="rotate(-48 59.5 68.5)"/>
            <ellipse cx="65.5" cy="58.5" rx="4.2" ry="1.7" transform="rotate(-68 65.5 58.5)"/>
            <ellipse cx="68.4" cy="47.5" rx="3.7" ry="1.55" transform="rotate(-83 68.4 47.5)"/>
            <ellipse cx="69.3" cy="37.5" rx="3.1" ry="1.4" transform="rotate(-92 69.3 37.5)"/>
        </g>
        <svg x="20" y="16" width="44" height="44" viewBox="0 0 24 24">
            <path fill="url(#waGreen)" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/>
            <path fill="#FBF7EE" d="M17.5 14.38c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51-.17-.01-.37-.01-.57-.01-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.19 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z"/>
        </svg>
        <g transform="translate(19,26)"><path class="wa-spk s1" fill="#EBD298" d="M0-5 C.8-1.5 1.5-.8 5 0 C1.5.8 .8 1.5 0 5 C-.8 1.5 -1.5 .8 -5 0 C-1.5-.8 -.8-1.5 0-5 Z"/></g>
        <g transform="translate(67,56) scale(.82)"><path class="wa-spk s2" fill="#EBD298" d="M0-5 C.8-1.5 1.5-.8 5 0 C1.5.8 .8 1.5 0 5 C-.8 1.5 -1.5 .8 -5 0 C-1.5-.8 -.8-1.5 0-5 Z"/></g>
        <g transform="translate(65,19) scale(.55)"><path class="wa-spk s3" fill="#F0DFA8" d="M0-5 C.8-1.5 1.5-.8 5 0 C1.5.8 .8 1.5 0 5 C-.8 1.5 -1.5 .8 -5 0 C-1.5-.8 -.8-1.5 0-5 Z"/></g>
        <g transform="translate(27,65) scale(.6)"><path class="wa-spk s4" fill="#F0DFA8" d="M0-5 C.8-1.5 1.5-.8 5 0 C1.5.8 .8 1.5 0 5 C-.8 1.5 -1.5 .8 -5 0 C-1.5-.8 -.8-1.5 0-5 Z"/></g>
    </svg>
</button>

<script>
(function () {
    var KEY_DISMISSED = 'ba_wa_dismissed';
    var fab   = document.getElementById('ba-wa-fab');
    var pop   = document.getElementById('ba-wa-pop');
    if (!fab || !pop) return;

    function open() {
        pop.hidden = false;
        // Fuerza reflow para que la transición se aplique al añadir la clase
        void pop.offsetWidth;
        pop.classList.add('is-open');
        fab.setAttribute('aria-expanded', 'true');
    }
    function close() {
        pop.classList.remove('is-open');
        fab.setAttribute('aria-expanded', 'false');
        try { sessionStorage.setItem(KEY_DISMISSED, '1'); } catch (e) {}
        // Espera al fin de la transición antes de ocultarlo del árbol accesible
        setTimeout(function () {
            if (!pop.classList.contains('is-open')) pop.hidden = true;
        }, 320);
    }
    function toggle() {
        if (pop.classList.contains('is-open')) close();
        else open();
    }

    fab.addEventListener('click', toggle);
    pop.querySelector('[data-ba-wa-close]').addEventListener('click', close);
    // Al hacer clic en el CTA de WhatsApp, marca como visto para que no reabra
    var cta = pop.querySelector('[data-ba-wa-cta]');
    if (cta) cta.addEventListener('click', function () {
        try { sessionStorage.setItem(KEY_DISMISSED, '1'); } catch (e) {}
    });
    // Escape cierra
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && pop.classList.contains('is-open')) close();
    });
})();
</script>
@endif
