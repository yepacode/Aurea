@extends('layouts.app')

@section('title', 'Mis puntos | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
@php
    // Etiquetas y colores para el tipo de movimiento.
    $typeMap = [
        'earned'   => ['Ganados',   '#3B7A3B', '#EAF3EA'],
        'redeemed' => ['Canjeados', '#8A6E2E', '#FBF0D5'],
        'expired'  => ['Expirados', '#C0554A', '#F8E9E6'],
        'adjusted' => ['Ajuste',    '#6B6157', '#EFE7D8'],
    ];
    $pill = function ($map, $key) {
        $p = $map[$key] ?? [ucfirst((string) $key), '#6B6157', '#EFE7D8'];
        return "display:inline-block;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:600;color:{$p[1]};background:{$p[2]};";
    };
    $fmt = fn (int $n) => number_format($n, 0, ',', '.');
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
                Mis puntos <span aria-hidden="true">⭐</span>
            </h1>
            <p style="margin:0 0 22px;color:#6B6157;font-size:14px;">
                Ganas puntos con cada compra pagada. Muy pronto podrás canjearlos por descuentos.
            </p>

            {{-- Tarjeta balance --}}
            <div style="background:linear-gradient(135deg,#FFF8E8,#F6E6C0 55%,#EBCF90);border:1px solid #E0BE77;border-radius:22px;padding:clamp(24px,4vw,36px);box-shadow:0 20px 48px -30px rgba(190,154,83,.55);text-align:center;">
                <p style="margin:0;font-family:'Montserrat',sans-serif;font-size:12px;letter-spacing:.15em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">
                    Tus puntos disponibles
                </p>
                <p style="margin:10px 0 0;font-family:'Playfair Display',serif;font-size:clamp(48px,8vw,72px);font-weight:800;color:#7A5E1C;line-height:1;letter-spacing:-.02em;">
                    {{ $fmt($balance) }}
                </p>
                <p style="margin:8px 0 0;color:#8A6E2E;font-size:13px;">puntos ⭐</p>

                @if($expiringSoon > 0)
                    <p style="margin:16px 0 0;display:inline-block;background:rgba(255,255,255,.6);border:1px solid rgba(190,154,83,.4);border-radius:9999px;padding:6px 14px;font-size:12px;color:#8A6E2E;font-weight:600;">
                        ⏳ {{ $fmt($expiringSoon) }} puntos vencen en menos de 30 días
                    </p>
                @endif
            </div>

            {{-- Contadores secundarios --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-top:18px;">
                <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:14px;padding:16px 18px;">
                    <p style="margin:0;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">Ganados totales</p>
                    <p style="margin:6px 0 0;font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:#2E2A26;">{{ $fmt($earnedTotal) }}</p>
                </div>
                <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:14px;padding:16px 18px;">
                    <p style="margin:0;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">Canjeados totales</p>
                    <p style="margin:6px 0 0;font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:#2E2A26;">{{ $fmt($redeemedTotal) }}</p>
                </div>
                <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:14px;padding:16px 18px;">
                    <p style="margin:0;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">Expiran pronto (30 d)</p>
                    <p style="margin:6px 0 0;font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:{{ $expiringSoon > 0 ? '#C0554A' : '#2E2A26' }};">{{ $fmt($expiringSoon) }}</p>
                </div>
            </div>

            {{-- Historial de movimientos --}}
            <div style="margin-top:26px;background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(18px,3vw,24px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                <h2 style="font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:#2E2A26;margin:0 0 14px;">
                    Historial de movimientos
                </h2>

                @if($movements->isEmpty())
                    <div style="text-align:center;padding:28px 12px;">
                        <p style="font-size:36px;margin:0;">🪙</p>
                        <p style="margin:10px 0 0;color:#6B6157;font-size:14px;">Aún no tienes movimientos. Al pagar tu próxima compra, tus puntos aparecerán aquí.</p>
                        <a href="{{ route('products.index') }}"
                           style="display:inline-block;margin-top:14px;padding:10px 22px;border-radius:9999px;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);text-decoration:none;">
                            Explorar productos
                        </a>
                    </div>
                @else
                    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;margin:0 -6px;padding:0 6px;">
                        <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:560px;">
                            <thead>
                                <tr style="text-align:left;color:#8A6E2E;font-size:11px;letter-spacing:.1em;text-transform:uppercase;">
                                    <th style="padding:10px 8px;font-weight:700;">Fecha</th>
                                    <th style="padding:10px 8px;font-weight:700;">Tipo</th>
                                    <th style="padding:10px 8px;font-weight:700;text-align:right;">Puntos</th>
                                    <th style="padding:10px 8px;font-weight:700;">Pedido</th>
                                    <th style="padding:10px 8px;font-weight:700;">Nota</th>
                                    <th style="padding:10px 8px;font-weight:700;">Vence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $m)
                                    @php
                                        $isPositive = $m->points > 0;
                                        $signColor  = $isPositive ? '#3B7A3B' : '#C0554A';
                                        $sign       = $isPositive ? '+' : '−';
                                    @endphp
                                    <tr style="border-top:1px solid #EFE7D8;">
                                        <td style="padding:12px 8px;color:#2E2A26;white-space:nowrap;">
                                            {{ $m->created_at->format('d/m/Y') }}
                                        </td>
                                        <td style="padding:12px 8px;">
                                            <span style="{{ $pill($typeMap, $m->type) }}">
                                                {{ ($typeMap[$m->type][0] ?? ucfirst((string) $m->type)) }}
                                            </span>
                                        </td>
                                        <td style="padding:12px 8px;text-align:right;font-weight:700;color:{{ $signColor }};white-space:nowrap;">
                                            {{ $sign }}{{ $fmt(abs((int) $m->points)) }}
                                        </td>
                                        <td style="padding:12px 8px;white-space:nowrap;">
                                            @if($m->order_id)
                                                <a href="{{ route('account.order', $m->order_id) }}"
                                                   style="color:#BE9A53;font-weight:600;text-decoration:none;">
                                                    #{{ $m->order_id }}
                                                </a>
                                            @else
                                                <span style="color:#B7AFA1;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding:12px 8px;color:#6B6157;">
                                            {{ $m->note ?? '—' }}
                                        </td>
                                        <td style="padding:12px 8px;color:#6B6157;white-space:nowrap;">
                                            @if($m->expires_at)
                                                {{ $m->expires_at->format('d/m/Y') }}
                                            @else
                                                <span style="color:#B7AFA1;">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:18px;">
                        {{ $movements->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
@endsection
