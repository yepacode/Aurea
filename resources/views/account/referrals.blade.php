@extends('layouts.app')

@section('title', 'Recomienda y gana | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $fmt = fn (int $n) => number_format($n, 0, ',', '.');

    $statusMap = [
        'pending'   => ['Pendiente',   '#8A6E2E', '#FBF0D5'],
        'completed' => ['Completada',  '#3B7A3B', '#EAF3EA'],
    ];
    $pill = function ($map, $key) {
        $p = $map[$key] ?? [ucfirst((string) $key), '#6B6157', '#EFE7D8'];
        return "display:inline-block;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:600;color:{$p[1]};background:{$p[2]};";
    };
@endphp

<section style="padding:clamp(32px,5vw,60px) 20px;background:#FBF8F2;min-height:60vh;">
    <div style="max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap;gap:26px;align-items:flex-start;">

        {{-- Sidebar --}}
        <div style="flex:1 1 220px;min-width:220px;max-width:280px;">
            @include('account._nav')
        </div>

        {{-- Main --}}
        <div style="flex:3 1 480px;min-width:0;">

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3.5vw,32px);font-weight:700;color:#2E2A26;margin:0 0 6px;">
                💛 Recomienda Áurea y ganen las 2
            </h1>
            <p style="margin:0 0 22px;color:#6B6157;font-size:14px;line-height:1.55;">
                Comparte tu código con tus amigas. Cuando ellas hagan su primera compra:
                <strong>tú ganas 500 puntos ⭐</strong> y <strong>ellas también reciben 500 puntos de bienvenida</strong>.
            </p>

            {{-- Contador --}}
            <p style="margin:0 0 18px;color:#8A6E2E;font-size:13px;font-weight:600;">
                Has referido {{ $fmt($totalReferrals) }}
                {{ $totalReferrals === 1 ? 'amiga' : 'amigas' }}
                {{-- muestra ganancia solo si algo se ha completado --}}
                @if($pointsEarned > 0)
                    — has ganado <span style="color:#3B7A3B;">{{ $fmt($pointsEarned) }} puntos ⭐</span>
                @endif
            </p>

            {{-- Tarjeta: tu código y link --}}
            <div style="background:linear-gradient(135deg,#FFF8E8,#F6E6C0 55%,#EBCF90);border:1px solid #E0BE77;border-radius:22px;padding:clamp(24px,4vw,34px);box-shadow:0 20px 48px -30px rgba(190,154,83,.55);text-align:center;">
                <p style="margin:0;font-family:'Montserrat',sans-serif;font-size:12px;letter-spacing:.15em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">
                    Tu código de referida
                </p>
                <p style="margin:12px 0 6px;font-family:'Playfair Display',serif;font-size:clamp(34px,6vw,52px);font-weight:800;color:#7A5E1C;line-height:1;letter-spacing:.05em;">
                    {{ $customer->referral_code }}
                </p>
                <p style="margin:14px 0 0;color:#8A6E2E;font-size:13px;">Comparte este link con tus amigas:</p>

                <div style="margin:14px auto 0;max-width:420px;display:flex;gap:8px;align-items:stretch;">
                    <input type="text" readonly value="{{ $shareUrl }}"
                           id="ref-share-url"
                           style="flex:1;min-width:0;padding:11px 14px;font-size:13px;color:#2E2A26;border:1px solid rgba(122,94,28,.35);border-radius:12px;background:#fff;font-family:'Montserrat',sans-serif;outline:none;">
                    <button type="button"
                            id="ref-copy-btn"
                            data-copy="{{ $shareUrl }}"
                            style="padding:11px 18px;border:none;border-radius:12px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:700;color:#3B310F;background:#fff;box-shadow:0 8px 20px -14px rgba(122,94,28,.6);">
                        Copiar
                    </button>
                </div>

                <div style="margin-top:14px;display:flex;flex-wrap:wrap;gap:10px;justify-content:center;">
                    <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:9999px;text-decoration:none;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;color:#fff;background:#25D366;box-shadow:0 12px 26px -14px rgba(37,211,102,.9);">
                        <span aria-hidden="true">🟢</span> Compartir por WhatsApp
                    </a>
                    <button type="button"
                            data-copy="{{ $shareUrl }}"
                            class="ref-copy-secondary"
                            style="display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border:1px solid rgba(122,94,28,.35);border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;color:#3B310F;background:#fff;">
                        🔗 Copiar link
                    </button>
                </div>

                <p id="ref-copy-msg" style="min-height:18px;margin:10px 0 0;font-size:12px;color:#3B7A3B;font-weight:700;"></p>
            </div>

            {{-- Cómo funciona --}}
            <div style="margin-top:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
                <div style="background:#fff;border:1px solid #E5DCC9;border-radius:14px;padding:18px;">
                    <p style="margin:0;font-size:22px;">1️⃣</p>
                    <p style="margin:8px 0 0;font-family:'Playfair Display',serif;font-size:16px;color:#2E2A26;font-weight:700;">Comparte tu link</p>
                    <p style="margin:6px 0 0;color:#6B6157;font-size:13px;line-height:1.5;">Envíaselo por WhatsApp o pégalo en tu Instagram.</p>
                </div>
                <div style="background:#fff;border:1px solid #E5DCC9;border-radius:14px;padding:18px;">
                    <p style="margin:0;font-size:22px;">2️⃣</p>
                    <p style="margin:8px 0 0;font-family:'Playfair Display',serif;font-size:16px;color:#2E2A26;font-weight:700;">Ella se registra</p>
                    <p style="margin:6px 0 0;color:#6B6157;font-size:13px;line-height:1.5;">Al entrar por tu link queda vinculada a tu cuenta.</p>
                </div>
                <div style="background:#fff;border:1px solid #E5DCC9;border-radius:14px;padding:18px;">
                    <p style="margin:0;font-size:22px;">3️⃣</p>
                    <p style="margin:8px 0 0;font-family:'Playfair Display',serif;font-size:16px;color:#2E2A26;font-weight:700;">Ganan las 2</p>
                    <p style="margin:6px 0 0;color:#6B6157;font-size:13px;line-height:1.5;">Cuando ella pague su primera compra reciben 500 puntos cada una.</p>
                </div>
            </div>

            {{-- Tabla de referidas --}}
            <div style="margin-top:26px;background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(18px,3vw,24px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                <h2 style="font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:#2E2A26;margin:0 0 14px;">
                    Amigas que has referido
                </h2>

                @if($referrals->isEmpty())
                    <div style="text-align:center;padding:28px 12px;">
                        <p style="font-size:36px;margin:0;">💌</p>
                        <p style="margin:10px 0 0;color:#6B6157;font-size:14px;">Aún no has referido a nadie. ¡Comparte tu código por WhatsApp!</p>
                    </div>
                @else
                    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;margin:0 -6px;padding:0 6px;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:520px;">
                            <thead>
                                <tr style="text-align:left;color:#8A6E2E;font-size:11px;letter-spacing:.1em;text-transform:uppercase;">
                                    <th style="padding:10px 8px;font-weight:700;">Fecha</th>
                                    <th style="padding:10px 8px;font-weight:700;">Amiga</th>
                                    <th style="padding:10px 8px;font-weight:700;">Estado</th>
                                    <th style="padding:10px 8px;font-weight:700;text-align:right;">Puntos ganados</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referrals as $r)
                                    @php
                                        // Nombre parcial: "Ana M." — respeta la privacidad de la referida.
                                        $referredName = trim((string) ($r->referred->name ?? ''));
                                        $parts = preg_split('/\s+/', $referredName, -1, PREG_SPLIT_NO_EMPTY);
                                        $firstName = $parts[0] ?? '—';
                                        $lastInitial = isset($parts[1]) ? ' ' . strtoupper(substr($parts[1], 0, 1)) . '.' : '';
                                        $displayName = $firstName . $lastInitial;

                                        $earned = $r->status === 'completed' ? (int) $r->reward_referrer_points : 0;
                                    @endphp
                                    <tr style="border-top:1px solid #EFE7D8;">
                                        <td style="padding:12px 8px;color:#2E2A26;white-space:nowrap;">
                                            {{ $r->created_at->format('d/m/Y') }}
                                        </td>
                                        <td style="padding:12px 8px;color:#2E2A26;">
                                            {{ $displayName ?: '—' }}
                                        </td>
                                        <td style="padding:12px 8px;">
                                            <span style="{{ $pill($statusMap, $r->status) }}">
                                                {{ ($statusMap[$r->status][0] ?? ucfirst((string) $r->status)) }}
                                            </span>
                                        </td>
                                        <td style="padding:12px 8px;text-align:right;font-weight:700;color:{{ $earned > 0 ? '#3B7A3B' : '#B7AFA1' }};white-space:nowrap;">
                                            {{ $earned > 0 ? '+' . $fmt($earned) . ' ⭐' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:18px;">
                        {{ $referrals->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>

<script>
(function () {
    var msg = document.getElementById('ref-copy-msg');

    function flash(text) {
        if (!msg) return;
        msg.textContent = text;
        setTimeout(function () { if (msg) msg.textContent = ''; }, 2200);
    }

    function copy(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        // Fallback para http:// local.
        return new Promise(function (resolve, reject) {
            try {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'absolute';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                resolve();
            } catch (e) { reject(e); }
        });
    }

    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy') || '';
            copy(text).then(function () { flash('✔ Copiado — ¡compártelo!'); })
                      .catch(function () { flash('No pude copiar, hazlo manual.'); });
        });
    });
})();
</script>
@endsection
