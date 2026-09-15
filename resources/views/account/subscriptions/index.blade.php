@extends('layouts.app')

@section('title', 'Mis suscripciones | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $statusMap = [
        'active'    => ['Activa',    '#3B7A3B', '#EAF3EA'],
        'paused'    => ['Pausada',   '#8A6D1F', '#FBF0D5'],
        'cancelled' => ['Cancelada', '#C0554A', '#F8E9E6'],
    ];
    $pill = function ($map, $key) {
        $p = $map[$key] ?? [ucfirst((string) $key), '#6B6157', '#EFE7D8'];
        return "display:inline-block;padding:4px 12px;border-radius:9999px;font-size:12px;font-weight:600;color:{$p[1]};background:{$p[2]};";
    };
@endphp

<section style="padding:clamp(32px,5vw,60px) 20px;background:#FBF8F2;min-height:60vh;">
    <div style="max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap;gap:26px;align-items:flex-start;">
        <aside style="flex:1 1 240px;max-width:280px;">@include('account._nav')</aside>

        <div style="flex:2 1 620px;min-width:0;">
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(26px,3.5vw,34px);color:#2E2A26;margin:0 0 6px;">Mis suscripciones 🔄</h1>
            <p style="color:#6B6157;margin:0 0 24px;font-size:15px;">Aquí puedes gestionar tus rituales mensuales. Pausa, reanuda o cancela cuando quieras.</p>

            @if(session('success'))
                <div style="background:#EAF3EA;color:#3B7A3B;padding:12px 16px;border-radius:12px;margin-bottom:18px;">{{ session('success') }}</div>
            @endif
            @if(session('info'))
                <div style="background:#FBF0D5;color:#8A6D1F;padding:12px 16px;border-radius:12px;margin-bottom:18px;">{{ session('info') }}</div>
            @endif

            @forelse($subscriptions as $sub)
                <article style="background:#fff;border:1px solid #E5DCC9;border-radius:18px;padding:22px 24px;margin-bottom:16px;box-shadow:0 12px 32px -24px rgba(120,92,44,.35);">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                        <div>
                            <h2 style="font-family:'Playfair Display',serif;font-size:22px;color:#2E2A26;margin:0 0 4px;">{{ $sub->plan->name }}</h2>
                            <p style="margin:0;color:#8B7B5C;font-size:13px;">Cada {{ $sub->plan->interval_days }} días · {{ $sub->total_deliveries }} entrega(s) realizada(s)</p>
                        </div>
                        <span style="{{ $pill($statusMap, $sub->status) }}">{{ $statusMap[$sub->status][0] ?? $sub->status }}</span>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin:16px 0;padding:14px 18px;background:#FBF6EC;border-radius:12px;">
                        <div>
                            <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#8B7B5C;">Próxima entrega</p>
                            <p style="margin:4px 0 0;font-weight:600;color:#2E2A26;">
                                {{ $sub->status === 'cancelled' ? '—' : $sub->next_delivery_at->translatedFormat('l j \d\e F') }}
                            </p>
                        </div>
                        <div>
                            <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#8B7B5C;">Precio del ritual</p>
                            <p style="margin:4px 0 0;font-weight:600;color:#2E2A26;">${{ number_format((float) $sub->plan->base_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#8B7B5C;">Método de pago</p>
                            <p style="margin:4px 0 0;font-weight:600;color:#2E2A26;">{{ $sub->payment_method === 'epayco' ? 'ePayco' : 'Contra entrega' }}</p>
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <a href="{{ route('account.subscriptions.show', $sub) }}"
                           style="padding:10px 18px;border-radius:999px;background:#2E2A26;color:#FBF8F2;text-decoration:none;font-size:13px;font-weight:600;">Ver detalles</a>

                        @if($sub->status === 'active')
                            <form method="POST" action="{{ route('account.subscriptions.pause', $sub) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="padding:10px 18px;border-radius:999px;background:#FBF0D5;color:#8A6D1F;border:none;font-size:13px;font-weight:600;cursor:pointer;">Pausar</button>
                            </form>
                        @elseif($sub->status === 'paused')
                            <form method="POST" action="{{ route('account.subscriptions.resume', $sub) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="padding:10px 18px;border-radius:999px;background:#EAF3EA;color:#3B7A3B;border:none;font-size:13px;font-weight:600;cursor:pointer;">Reanudar</button>
                            </form>
                        @endif

                        @if($sub->status !== 'cancelled')
                            <form method="POST" action="{{ route('account.subscriptions.cancel', $sub) }}" style="display:inline;"
                                  onsubmit="return confirm('¿Seguro quieres cancelar esta suscripción? Podrás volver a suscribirte cuando quieras.');">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding:10px 18px;border-radius:999px;background:#F8E9E6;color:#C0554A;border:none;font-size:13px;font-weight:600;cursor:pointer;">Cancelar</button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div style="background:#fff;border:1px solid #E5DCC9;border-radius:18px;padding:40px 24px;text-align:center;color:#6B6157;">
                    <p style="margin:0 0 12px;">Aún no tienes ninguna suscripción activa.</p>
                    <a href="{{ route('subscriptions.index') }}"
                       style="display:inline-block;padding:12px 24px;border-radius:999px;background:#2E2A26;color:#FBF8F2;text-decoration:none;font-size:13px;font-weight:600;">
                        Descubrir rituales mensuales 🌸
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
