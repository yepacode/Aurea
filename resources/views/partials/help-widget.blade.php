{{-- =========================================================================
     Widget de Ayuda unificado — Belleza Áurea
     Un solo FAB dorado que abre un popup con dos pestañas:
       (1) Preguntas frecuentes (chatbot FAQ)
       (2) WhatsApp (estado online/offline + CTA a wa.me)
     Reemplaza a partials.chatbot y partials.whatsapp-widget (mantén ambos
     archivos para retro-compat; simplemente ya no se incluyen en el layout).
     ========================================================================= --}}
@php
    $__hw_wa       = \App\Models\ContactPageSetting::getCurrent();
    $__hw_waOn     = $__hw_wa->whatsapp_widget_enabled ?? true;
    $__hw_online   = $__hw_waOn ? $__hw_wa->isWhatsappOnline() : false;
    $__hw_waUrl    = \App\Models\ContactPageSetting::whatsappUrl();
    $__hw_waWelc   = $__hw_waOn ? ($__hw_online ? $__hw_wa->whatsappWelcomeOnline() : $__hw_wa->whatsappWelcomeOffline()) : '';
    $__hw_waHours  = $__hw_waOn ? $__hw_wa->whatsappHoursSummary() : '';
@endphp

<style>
    /* ========= FAB dorado unificado ========= */
    .ba-hw-fab{position:fixed;bottom:24px;right:24px;z-index:99;width:62px;height:62px;
        display:flex;align-items:center;justify-content:center;border:0;padding:0;cursor:pointer;
        background:linear-gradient(135deg,#EBCF90 0%,#D9B56D 55%,#BE9A53 100%);
        color:#2E2A26;border-radius:9999px;
        box-shadow:0 14px 32px -10px rgba(140,110,56,.55),0 4px 12px rgba(46,42,38,.18);
        transition:transform .28s cubic-bezier(.2,.8,.3,1),box-shadow .28s ease;
        animation:ba-hw-float 4.6s ease-in-out infinite;}
    @keyframes ba-hw-float{0%,100%{transform:translateY(0);}50%{transform:translateY(-4px);}}
    .ba-hw-fab:hover{transform:scale(1.06) rotate(-2deg);
        box-shadow:0 18px 36px -12px rgba(140,110,56,.65),0 6px 14px rgba(46,42,38,.22);}
    .ba-hw-fab svg{width:28px;height:28px;stroke-width:1.7;
        filter:drop-shadow(0 1px 1px rgba(255,255,255,.4));}

    /* Badge de estado (En línea / Offline) sobre el FAB */
    .ba-hw-fab__badge{position:absolute;top:-6px;right:-8px;display:inline-flex;align-items:center;gap:5px;
        padding:3px 8px 3px 6px;border-radius:9999px;
        font:600 10px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.06em;text-transform:uppercase;
        background:#FBF7EE;color:#2E2A26;border:1px solid rgba(46,42,38,.12);
        box-shadow:0 4px 12px rgba(46,42,38,.18);white-space:nowrap;pointer-events:none;}
    .ba-hw-fab__badge::before{content:"";width:7px;height:7px;border-radius:50%;background:#9AA79A;box-shadow:0 0 0 2px rgba(154,167,154,.25);}
    .ba-hw-fab[data-online="true"] .ba-hw-fab__badge::before{background:#25D366;box-shadow:0 0 0 2px rgba(37,211,102,.28);animation:ba-hw-pulse 1.8s ease-in-out infinite;}
    @keyframes ba-hw-pulse{0%,100%{box-shadow:0 0 0 2px rgba(37,211,102,.28);}50%{box-shadow:0 0 0 6px rgba(37,211,102,0);}}

    /* Puntito "sin ver" en el FAB (primera vez en la sesión) */
    .ba-hw-fab__dot{position:absolute;top:6px;right:6px;width:10px;height:10px;border-radius:9999px;
        background:#D9534F;border:2px solid #FBF7EE;
        animation:ba-hw-bounce 1.8s ease-in-out infinite;}
    @keyframes ba-hw-bounce{0%,100%{transform:scale(1);}50%{transform:scale(1.2);}}

    /* ========= Popup ========= */
    .ba-hw-pop{position:fixed;bottom:100px;right:24px;z-index:100;
        width:min(360px,calc(100vw - 32px));height:min(580px,calc(100vh - 140px));
        background:linear-gradient(180deg,#FEFCF8 0%,#F8F2E8 100%);
        border:1px solid rgba(217,181,109,.28);border-radius:22px;
        box-shadow:0 40px 90px -30px rgba(46,42,38,.55),0 8px 24px -12px rgba(0,0,0,.12);
        overflow:hidden;transform-origin:bottom right;display:flex;flex-direction:column;
        opacity:0;pointer-events:none;transform:translateY(14px) scale(.96);
        transition:opacity .3s ease,transform .3s cubic-bezier(.2,.8,.3,1);}
    .ba-hw-pop.is-open{opacity:1;pointer-events:auto;transform:translateY(0) scale(1);}

    /* Header dorado */
    .ba-hw-pop__hd{padding:14px 16px;flex-shrink:0;
        background:linear-gradient(120deg,#BE9A53 0%,#EBD298 55%,#BE9A53 100%);
        color:#2E2A26;position:relative;overflow:hidden;}
    .ba-hw-pop__hd::before{content:"";position:absolute;inset:0;
        background:radial-gradient(ellipse at 20% 0%,rgba(255,255,255,.35),transparent 60%);
        pointer-events:none;}
    .ba-hw-pop__hd-row{display:flex;align-items:center;gap:12px;position:relative;}
    .ba-hw-pop__hd-logo{width:38px;height:38px;border-radius:9999px;flex-shrink:0;
        background:#2E2A26;color:#EBD298;display:flex;align-items:center;justify-content:center;
        font:700 18px/1 'Playfair Display',serif;
        box-shadow:inset 0 0 0 2px rgba(235,210,152,.4);}
    .ba-hw-pop__hd-title{font:600 15px/1.2 'Playfair Display',serif;letter-spacing:.02em;}
    .ba-hw-pop__hd-sub{margin-top:3px;display:inline-flex;align-items:center;gap:6px;
        font:500 11px/1.2 'Montserrat',system-ui,sans-serif;letter-spacing:.05em;color:rgba(46,42,38,.72);}
    .ba-hw-pop__hd-sub::before{content:"";width:7px;height:7px;border-radius:50%;background:#9AA79A;box-shadow:0 0 0 2px rgba(154,167,154,.25);}
    .ba-hw-pop[data-online="true"] .ba-hw-pop__hd-sub::before{background:#25D366;box-shadow:0 0 0 2px rgba(37,211,102,.3);}
    .ba-hw-pop__close{margin-left:auto;background:rgba(46,42,38,.1);border:0;
        color:#2E2A26;cursor:pointer;width:32px;height:32px;border-radius:9999px;
        display:flex;align-items:center;justify-content:center;transition:background .2s ease;}
    .ba-hw-pop__close:hover{background:rgba(46,42,38,.2);}

    /* Tabs superiores */
    .ba-hw-tabs{display:flex;flex-shrink:0;background:rgba(46,42,38,.04);border-bottom:1px solid rgba(217,181,109,.18);}
    .ba-hw-tab{flex:1;padding:11px 8px;background:none;border:0;cursor:pointer;
        font:600 12px/1.2 'Montserrat',system-ui,sans-serif;letter-spacing:.04em;text-transform:uppercase;
        color:#8A7A5F;display:inline-flex;align-items:center;justify-content:center;gap:6px;
        border-bottom:2px solid transparent;transition:color .2s ease,border-color .2s ease,background .2s ease;}
    .ba-hw-tab:hover{color:#2E2A26;background:rgba(217,181,109,.06);}
    .ba-hw-tab.is-active{color:#2E2A26;border-bottom-color:#BE9A53;background:#FEFCF8;}
    .ba-hw-tab__ic{font-size:14px;line-height:1;}

    /* Body */
    .ba-hw-pop__body{flex:1;overflow-y:auto;position:relative;
        scrollbar-width:thin;scrollbar-color:rgba(190,154,83,.4) transparent;}
    .ba-hw-pop__body::-webkit-scrollbar{width:6px;}
    .ba-hw-pop__body::-webkit-scrollbar-thumb{background:rgba(190,154,83,.4);border-radius:3px;}

    /* --- Panel FAQ --- */
    .ba-hw-faq{padding:12px 14px;}
    .ba-hw-search{margin-bottom:12px;position:relative;}
    .ba-hw-search input{width:100%;padding:9px 12px 9px 34px;border-radius:10px;
        background:#fff;border:1px solid rgba(46,42,38,.1);
        font:400 13px/1.2 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238C6E38' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><circle cx='11' cy='11' r='7'/><line x1='21' y1='21' x2='16.65' y2='16.65'/></svg>");
        background-repeat:no-repeat;background-position:11px center;background-size:16px;}
    .ba-hw-search input:focus{outline:none;border-color:#BE9A53;box-shadow:0 0 0 3px rgba(217,181,109,.2);}

    .ba-hw-cats{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .ba-hw-cat{display:flex;flex-direction:column;align-items:flex-start;gap:6px;
        padding:14px 12px;background:#fff;border:1px solid rgba(217,181,109,.2);
        border-radius:14px;cursor:pointer;text-align:left;
        transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
        font-family:'Montserrat',system-ui,sans-serif;color:#2E2A26;}
    .ba-hw-cat:hover{transform:translateY(-2px);border-color:#BE9A53;
        box-shadow:0 8px 20px -8px rgba(140,110,56,.35);}
    .ba-hw-cat__emoji{font-size:26px;line-height:1;}
    .ba-hw-cat__name{font:600 13px/1.2 'Montserrat',system-ui,sans-serif;}
    .ba-hw-cat__count{font:400 10.5px/1 'Montserrat',system-ui,sans-serif;color:#8A7A5F;}

    .ba-hw-back{display:inline-flex;align-items:center;gap:6px;background:none;border:0;
        cursor:pointer;color:#8C6E38;font:600 12px/1 'Montserrat',system-ui,sans-serif;
        letter-spacing:.04em;text-transform:uppercase;padding:0 0 10px;}
    .ba-hw-back:hover{color:#2E2A26;}
    .ba-hw-qs{display:flex;flex-direction:column;gap:6px;}
    .ba-hw-q{display:flex;align-items:center;justify-content:space-between;gap:10px;
        padding:12px 14px;background:#fff;border:1px solid rgba(217,181,109,.18);
        border-radius:12px;cursor:pointer;text-align:left;
        font:500 13px/1.35 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        transition:border-color .2s ease,background .2s ease;}
    .ba-hw-q:hover{border-color:#BE9A53;background:#FEFCF8;}
    .ba-hw-q__arrow{flex-shrink:0;color:#BE9A53;}
    .ba-hw-answer__cat{display:inline-flex;align-items:center;gap:6px;
        padding:4px 10px;border-radius:9999px;background:rgba(217,181,109,.15);
        color:#8C6E38;font:600 10.5px/1 'Montserrat',system-ui,sans-serif;
        letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px;}
    .ba-hw-answer__q{font:600 15px/1.3 'Playfair Display',serif;color:#2E2A26;margin-bottom:12px;}
    .ba-hw-answer__body{background:#fff;border:1px solid rgba(217,181,109,.2);border-radius:14px;
        padding:14px 16px;font:400 13.5px/1.55 'Montserrat',system-ui,sans-serif;
        color:#3B342C;white-space:pre-wrap;}
    .ba-hw-feedback{margin-top:14px;display:flex;align-items:center;gap:8px;justify-content:center;
        font:500 12px 'Montserrat',system-ui,sans-serif;color:#5A4B33;}
    .ba-hw-feedback button{background:#fff;border:1px solid rgba(217,181,109,.3);
        padding:6px 12px;border-radius:9999px;cursor:pointer;
        font:600 12px 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        transition:background .2s ease,transform .2s ease;}
    .ba-hw-feedback button:hover{background:#F8F2E8;transform:translateY(-1px);}
    .ba-hw-feedback button.is-active{background:#BE9A53;color:#fff;border-color:#BE9A53;}
    .ba-hw-search-empty{text-align:center;color:#8A7A5F;padding:24px 12px;
        font:400 13px/1.5 'Montserrat',system-ui,sans-serif;}
    .ba-hw-loading{display:flex;align-items:center;justify-content:center;padding:30px;color:#8A7A5F;
        font:400 12.5px 'Montserrat',system-ui,sans-serif;}
    .ba-hw-loading::before{content:"";width:16px;height:16px;border-radius:9999px;margin-right:10px;
        border:2px solid rgba(190,154,83,.25);border-top-color:#BE9A53;
        animation:ba-hw-spin .8s linear infinite;}
    @keyframes ba-hw-spin{to{transform:rotate(360deg);}}

    /* CTA "Hablar con humana" al final del panel FAQ */
    .ba-hw-escalate{margin-top:14px;padding:12px 14px;text-align:center;
        background:#2E2A26;color:rgba(251,247,238,.85);border-radius:14px;}
    .ba-hw-escalate p{font:400 11.5px/1.4 'Montserrat',system-ui,sans-serif;margin-bottom:8px;}
    .ba-hw-escalate button{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;
        background:#25D366;color:#fff;border:0;border-radius:9999px;cursor:pointer;
        font:600 12.5px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.02em;
        box-shadow:0 6px 14px -4px rgba(37,211,102,.55);
        transition:background .2s ease,transform .2s ease;}
    .ba-hw-escalate button:hover{background:#1FBD5A;transform:translateY(-1px);}
    .ba-hw-escalate svg{width:14px;height:14px;}

    /* --- Panel WhatsApp --- */
    .ba-hw-wa{padding:18px;}
    .ba-hw-wa__bubble{background:#fff;border:1px solid rgba(46,42,38,.08);border-radius:14px 14px 14px 4px;
        padding:12px 14px;font:400 14px/1.45 'Montserrat',system-ui,sans-serif;color:#2E2A26;
        box-shadow:0 4px 12px -4px rgba(46,42,38,.08);}
    .ba-hw-wa__cta{display:flex;align-items:center;justify-content:center;gap:10px;
        margin-top:14px;padding:14px 18px;width:100%;min-height:48px;
        background:#25D366;color:#fff;border-radius:12px;text-decoration:none;
        font:600 14px/1 'Montserrat',system-ui,sans-serif;letter-spacing:.02em;
        box-shadow:0 8px 22px -6px rgba(37,211,102,.55);
        transition:background .2s ease,transform .2s ease,box-shadow .2s ease;}
    .ba-hw-wa__cta:hover{background:#1FBD5A;transform:translateY(-1px);box-shadow:0 12px 26px -8px rgba(37,211,102,.65);}
    .ba-hw-wa__cta svg{width:18px;height:18px;flex-shrink:0;}
    .ba-hw-wa__hours{margin-top:12px;padding:10px 12px;border-radius:10px;
        background:rgba(217,181,109,.1);border:1px solid rgba(217,181,109,.22);
        font:400 11.5px/1.4 'Montserrat',system-ui,sans-serif;color:#5A4B33;}
    .ba-hw-wa__hours strong{color:#8C6E38;font-weight:600;letter-spacing:.04em;text-transform:uppercase;font-size:10.5px;}

    /* Accesibilidad y responsive */
    @media (prefers-reduced-motion: reduce){
        .ba-hw-fab,.ba-hw-fab__dot,.ba-hw-fab[data-online="true"] .ba-hw-fab__badge::before,
        .ba-hw-pop[data-online="true"] .ba-hw-pop__hd-sub::before{animation:none;}
        .ba-hw-pop{transition:opacity .15s ease;}
    }
    @media (max-width:520px){
        .ba-hw-fab{width:60px;height:60px;bottom:16px;right:16px;}
        .ba-hw-fab svg{width:26px;height:26px;}
        .ba-hw-fab__badge{top:-4px;right:-4px;font-size:9px;padding:2px 7px 2px 5px;}
        .ba-hw-pop{bottom:0;right:0;left:0;top:0;width:100vw;height:100vh;
            max-width:none;max-height:none;border-radius:0;}
    }
</style>

<div x-data="baHelpWidget()" x-cloak>

    {{-- ============ POPUP ============ --}}
    <div class="ba-hw-pop" :class="{ 'is-open': open }" role="dialog" aria-modal="false"
         aria-label="Ayuda Belleza Áurea" data-online="{{ $__hw_online ? 'true' : 'false' }}"
         x-show="open" x-transition.opacity>

        {{-- Header --}}
        <div class="ba-hw-pop__hd">
            <div class="ba-hw-pop__hd-row">
                <div class="ba-hw-pop__hd-logo" aria-hidden="true">A</div>
                <div>
                    <div class="ba-hw-pop__hd-title">💛 Áurea — ¿En qué te ayudo?</div>
                    <div class="ba-hw-pop__hd-sub">{{ $__hw_online ? 'En línea' : 'Fuera de horario' }}</div>
                </div>
                <button type="button" class="ba-hw-pop__close" @click="close()" aria-label="Cerrar ayuda">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="ba-hw-tabs" role="tablist" aria-label="Modo de ayuda">
            <button type="button" class="ba-hw-tab" :class="{ 'is-active': tab === 'faq' }"
                    role="tab" :aria-selected="tab === 'faq'" @click="switchTab('faq')">
                <span class="ba-hw-tab__ic">📖</span>
                Preguntas frecuentes
            </button>
            @if($__hw_waOn)
            <button type="button" class="ba-hw-tab" :class="{ 'is-active': tab === 'whatsapp' }"
                    role="tab" :aria-selected="tab === 'whatsapp'" @click="switchTab('whatsapp')">
                <span class="ba-hw-tab__ic">💚</span>
                WhatsApp
            </button>
            @endif
        </div>

        {{-- Body --}}
        <div class="ba-hw-pop__body">

            {{-- ==================== TAB FAQ ==================== --}}
            <div class="ba-hw-faq" x-show="tab === 'faq'" role="tabpanel">
                <div class="ba-hw-search">
                    <input type="search" placeholder="Buscar en preguntas frecuentes..."
                           x-model="searchQuery" @input.debounce.300ms="runSearch()"
                           aria-label="Buscar preguntas frecuentes">
                </div>

                <template x-if="loading">
                    <div class="ba-hw-loading">Cargando…</div>
                </template>

                {{-- Resultados de búsqueda --}}
                <template x-if="!loading && searchQuery.trim().length >= 2">
                    <div>
                        <template x-if="searchResults.length === 0">
                            <div class="ba-hw-search-empty">
                                No encontramos resultados para «<span x-text="searchQuery"></span>».<br>
                                @if($__hw_waOn) Prueba a escribirnos por WhatsApp 💛 @endif
                            </div>
                        </template>
                        <div class="ba-hw-qs">
                            <template x-for="r in searchResults" :key="r.id">
                                <button type="button" class="ba-hw-q" @click="openAnswer(r)">
                                    <span x-text="(r.category?.emoji || '') + ' ' + r.question"></span>
                                    <svg class="ba-hw-q__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Categorías --}}
                <template x-if="!loading && searchQuery.trim().length < 2 && faqView === 'categories'">
                    <div class="ba-hw-cats">
                        <template x-for="cat in categories" :key="cat.id">
                            <button type="button" class="ba-hw-cat" @click="openCategory(cat)">
                                <span class="ba-hw-cat__emoji" x-text="cat.emoji || '💬'"></span>
                                <span class="ba-hw-cat__name" x-text="cat.name"></span>
                                <span class="ba-hw-cat__count"><span x-text="cat.count"></span> preguntas</span>
                            </button>
                        </template>
                    </div>
                </template>

                {{-- Preguntas de categoría --}}
                <template x-if="!loading && searchQuery.trim().length < 2 && faqView === 'questions'">
                    <div>
                        <button type="button" class="ba-hw-back" @click="backToCategories()">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            <span x-text="(selectedCategory?.emoji || '') + ' ' + (selectedCategory?.name || '')"></span>
                        </button>
                        <div class="ba-hw-qs">
                            <template x-for="q in categoryFaqs" :key="q.id">
                                <button type="button" class="ba-hw-q" @click="openAnswer(q)">
                                    <span x-text="q.question"></span>
                                    <svg class="ba-hw-q__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Respuesta --}}
                <template x-if="!loading && searchQuery.trim().length < 2 && faqView === 'answer'">
                    <div>
                        <button type="button" class="ba-hw-back" @click="backFromAnswer()">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            Volver
                        </button>
                        <template x-if="selectedFaq?.category?.name">
                            <div class="ba-hw-answer__cat">
                                <span x-text="selectedFaq.category.emoji || ''"></span>
                                <span x-text="selectedFaq.category.name"></span>
                            </div>
                        </template>
                        <p class="ba-hw-answer__q" x-text="selectedFaq?.question"></p>
                        <div class="ba-hw-answer__body" x-text="selectedFaq?.answer"></div>
                        <div class="ba-hw-feedback">
                            <span>¿Te sirvió?</span>
                            <button type="button" @click="markHelpful(true)" :class="{ 'is-active': feedback === 'yes' }">👍 Útil</button>
                            <button type="button" @click="markHelpful(false)" :class="{ 'is-active': feedback === 'no' }">👎 No sirve</button>
                        </div>
                    </div>
                </template>

                {{-- Escalada: cambia al tab WhatsApp --}}
                @if($__hw_waOn)
                <div class="ba-hw-escalate">
                    <p>¿No encontraste tu respuesta?</p>
                    <button type="button" @click="switchTab('whatsapp')">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2z"/></svg>
                        Hablar con humana
                    </button>
                </div>
                @endif
            </div>

            {{-- ==================== TAB WHATSAPP ==================== --}}
            @if($__hw_waOn)
            <div class="ba-hw-wa" x-show="tab === 'whatsapp'" role="tabpanel">
                <div class="ba-hw-wa__bubble">{{ $__hw_waWelc }}</div>
                <a class="ba-hw-wa__cta" href="{{ $__hw_waUrl }}" target="_blank" rel="noopener"
                   @click="markSeen()" aria-label="Escribir por WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2zm5.46 12.38c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51-.17-.01-.37-.01-.57-.01-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.19 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z"/></svg>
                    Escribir por WhatsApp
                </a>
                <div class="ba-hw-wa__hours">
                    <strong>Horario de atención</strong><br>
                    {{ $__hw_waHours }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ============ FAB ============ --}}
    <button type="button" class="ba-hw-fab" @click="toggle()"
            data-online="{{ $__hw_online ? 'true' : 'false' }}"
            :aria-expanded="open" aria-label="Abrir ayuda de Belleza Áurea">
        <span class="ba-hw-fab__badge">{{ $__hw_online ? 'En línea' : 'Offline' }}</span>
        <template x-if="showDot">
            <span class="ba-hw-fab__dot" aria-hidden="true"></span>
        </template>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
        </svg>
    </button>

    {{-- Overlay para cerrar al hacer click fuera (invisible pero captura clicks) --}}
    <div x-show="open" x-cloak @click="close()" x-transition.opacity
         style="position:fixed;inset:0;z-index:98;background:transparent;" aria-hidden="true"></div>
</div>

<script>
window.baHelpWidget = function () {
    return {
        open: false,
        tab: 'faq',                    // 'faq' | 'whatsapp'
        loading: false,
        faqView: 'categories',         // 'categories' | 'questions' | 'answer'
        categories: [],
        categoryFaqs: [],
        selectedCategory: null,
        selectedFaq: null,
        searchQuery: '',
        searchResults: [],
        feedback: null,
        showDot: false,

        init() {
            try {
                this.showDot = !sessionStorage.getItem('ba_hw_seen');
                const saved = sessionStorage.getItem('ba_hw_state');
                if (saved) {
                    const s = JSON.parse(saved);
                    this.tab = s.tab || 'faq';
                    this.faqView = s.faqView || 'categories';
                    this.selectedCategory = s.selectedCategory || null;
                    this.selectedFaq = s.selectedFaq || null;
                    this.categoryFaqs = s.categoryFaqs || [];
                }
            } catch (e) {}
        },

        persist() {
            try {
                sessionStorage.setItem('ba_hw_state', JSON.stringify({
                    tab: this.tab,
                    faqView: this.faqView,
                    selectedCategory: this.selectedCategory,
                    selectedFaq: this.selectedFaq,
                    categoryFaqs: this.categoryFaqs,
                }));
            } catch (e) {}
        },

        markSeen() {
            this.showDot = false;
            try { sessionStorage.setItem('ba_hw_seen', '1'); } catch (e) {}
        },

        async toggle() {
            if (this.open) this.close();
            else await this.openPanel();
        },

        async openPanel() {
            this.open = true;
            this.markSeen();
            if (this.tab === 'faq' && this.categories.length === 0) {
                await this.loadCategories();
            }
        },

        close() {
            this.open = false;
            this.persist();
        },

        async switchTab(t) {
            this.tab = t;
            this.persist();
            if (t === 'faq' && this.categories.length === 0) {
                await this.loadCategories();
            }
        },

        async loadCategories() {
            this.loading = true;
            try {
                const r = await fetch('{{ route('faq.categories.json') }}', { headers: { 'Accept': 'application/json' } });
                const j = await r.json();
                this.categories = j.data || [];
            } catch (e) {
                this.categories = [];
            } finally {
                this.loading = false;
            }
        },

        async openCategory(cat) {
            this.selectedCategory = cat;
            this.faqView = 'questions';
            this.loading = true;
            try {
                const r = await fetch('/faq/categoria/' + encodeURIComponent(cat.slug), { headers: { 'Accept': 'application/json' } });
                const j = await r.json();
                this.categoryFaqs = (j.faqs || []).map(f => Object.assign({}, f, {
                    category: { name: cat.name, slug: cat.slug, emoji: cat.emoji },
                }));
            } catch (e) {
                this.categoryFaqs = [];
            } finally {
                this.loading = false;
                this.persist();
            }
        },

        backToCategories() {
            this.faqView = 'categories';
            this.selectedCategory = null;
            this.categoryFaqs = [];
            this.persist();
        },

        openAnswer(faq) {
            this.selectedFaq = faq;
            this.faqView = 'answer';
            this.feedback = null;
            this.searchQuery = '';
            this.searchResults = [];
            this.persist();
            this.trackView(faq.id);
        },

        backFromAnswer() {
            this.selectedFaq = null;
            this.feedback = null;
            this.faqView = this.selectedCategory ? 'questions' : 'categories';
            this.persist();
        },

        async runSearch() {
            const q = this.searchQuery.trim();
            if (q.length < 2) { this.searchResults = []; return; }
            try {
                const r = await fetch('{{ route('faq.search') }}?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' }
                });
                const j = await r.json();
                this.searchResults = j.data || [];
            } catch (e) { this.searchResults = []; }
        },

        async markHelpful(isHelpful) {
            if (!this.selectedFaq) return;
            this.feedback = isHelpful ? 'yes' : 'no';
            if (isHelpful) {
                try {
                    await fetch('/faq/' + this.selectedFaq.id + '/util', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                    });
                } catch (e) {}
            }
        },

        async trackView(id) {
            try {
                await fetch('/faq/' + id + '/view', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                });
            } catch (e) {}
        },
    };
};
</script>
