{{-- ============================================================
     POPUP DE BIENVENIDA — cupón 10% por suscripción (Alpine)
     Se muestra una sola vez por navegador (localStorage).
     ============================================================ --}}
<style>
    .aurea-welcome-ov{position:fixed;inset:0;z-index:95;background:rgba(46,42,38,.55);
        -webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);
        display:flex;align-items:center;justify-content:center;padding:20px;}
</style>
<div x-data="baWelcomePopup()" x-init="init()" x-cloak
     x-show="show"
     @keydown.escape.window="close()"
     class="aurea-welcome-ov"
     x-transition.opacity
     @click.self="close()">

    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="position:relative;width:100%;max-width:440px;background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);
                border:1px solid rgba(217,181,109,.35);border-radius:24px;overflow:hidden;
                box-shadow:0 46px 92px -40px rgba(120,92,44,.6),0 6px 22px -12px rgba(0,0,0,.12);">

        {{-- Borde dorado superior --}}
        <div style="height:5px;background:linear-gradient(90deg,#EBCF90,#D9B56D 50%,#BE9A53);"></div>

        {{-- Cerrar --}}
        <button type="button" @click="close()" aria-label="Cerrar"
                style="position:absolute;top:14px;right:16px;background:rgba(255,255,255,.7);border:1px solid rgba(217,181,109,.3);
                       width:32px;height:32px;border-radius:50%;font-size:18px;line-height:1;color:#8E7E70;cursor:pointer;
                       display:flex;align-items:center;justify-content:center;transition:all .2s;"
                onmouseover="this.style.color='#2E2A26';this.style.background='#fff'"
                onmouseout="this.style.color='#8E7E70';this.style.background='rgba(255,255,255,.7)'">&times;</button>

        <div style="padding:clamp(28px,4vw,40px) clamp(24px,4vw,38px) clamp(26px,4vw,36px);text-align:center;">

            <div style="font-size:11px;font-weight:600;letter-spacing:.24em;text-transform:uppercase;color:#BE9A53;margin-bottom:14px;">
                Belleza Áurea
            </div>

            <h2 style="font-family:'Playfair Display',serif;font-size:clamp(24px,4vw,30px);font-weight:700;color:#2E2A26;line-height:1.15;margin:0 0 12px;">
                10% en tu primera compra 🌸
            </h2>

            <p style="font-size:14.5px;color:#6B6157;line-height:1.6;margin:0 0 22px;">
                Suscríbete y recibe rituales de belleza, novedades y un cupón de bienvenida.
            </p>

            {{-- Éxito (tras enviar) --}}
            @if(session('success'))
            <div x-data="{ ok: false }"
                 x-init="if (window.location.hash === '#ba-welcome-ok') { ok = true; }"
                 x-show="ok" x-cloak
                 style="background:#EAF3EA;border:1px solid #C5E0C5;color:#3B7A3B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:16px;">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('leads.store') }}" @submit="markSeen()"
                  style="display:flex;flex-direction:column;gap:12px;">
                @csrf
                <input type="hidden" name="source" value="popup">
                <input type="hidden" name="redirect" value="{{ url()->current() }}#ba-welcome-ok">

                <input type="text" name="name" placeholder="Tu nombre (opcional)"
                       autocomplete="name" maxlength="255"
                       aria-label="Tu nombre para el cupón"
                       style="width:100%;background:#FFFDF9;border:1px solid #E5DCC9;border-radius:12px;padding:12px 15px;font-size:15px;color:#2E2A26;font-family:'Montserrat',sans-serif;outline:none;"
                       onfocus="this.style.borderColor='#D9B56D'" onblur="this.style.borderColor='#E5DCC9'">

                <input type="email" name="email" placeholder="Tu correo electrónico *" required
                       autocomplete="email" maxlength="255"
                       aria-label="Correo electrónico para recibir el cupón"
                       style="width:100%;background:#FFFDF9;border:1px solid #E5DCC9;border-radius:12px;padding:12px 15px;font-size:15px;color:#2E2A26;font-family:'Montserrat',sans-serif;outline:none;"
                       onfocus="this.style.borderColor='#D9B56D'" onblur="this.style.borderColor='#E5DCC9'">

                <button type="submit"
                        style="margin-top:4px;padding:14px 22px;border:none;border-radius:9999px;cursor:pointer;
                               font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;letter-spacing:.04em;color:#3B310F;
                               background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);
                               box-shadow:0 12px 26px -14px rgba(190,154,83,.9);transition:filter .2s,transform .2s;"
                        onmouseover="this.style.filter='brightness(1.04)';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.filter='none';this.style.transform='none'">
                    Quiero mi cupón
                </button>
            </form>

            <button type="button" @click="close()"
                    style="margin-top:14px;background:none;border:none;color:#9A8F82;font-size:13px;cursor:pointer;text-decoration:underline;font-family:'Montserrat',sans-serif;">
                No, gracias
            </button>
        </div>
    </div>
</div>

<script>
    function baWelcomePopup() {
        return {
            show: false,
            _key: 'aurea_welcome_seen',
            init() {
                try {
                    var seen = localStorage.getItem(this._key);
                    if (seen && (Date.now() - parseInt(seen, 10)) < 30 * 24 * 60 * 60 * 1000) return;
                } catch (e) { return; }
                var self = this;
                setTimeout(function () {
                    try {
                        var s = localStorage.getItem(self._key);
                        if (!s || (Date.now() - parseInt(s, 10)) >= 30 * 24 * 60 * 60 * 1000) self.show = true;
                    } catch (e) { self.show = true; }
                }, 5000);
            },
            markSeen() {
                try { localStorage.setItem(this._key, String(Date.now())); } catch (e) {}
            },
            close() {
                this.show = false;
                this.markSeen();
            },
        };
    }
</script>
