@extends('layouts.app')

@section('title', $bundle->name . ' | Kits Áurea')
@section('meta_description', 'Kit ' . $bundle->name . ' — combo con ' . $bundle->items->count() . ' productos y ' . $bundle->savings_percent . '% de ahorro. Belleza Áurea.')

@push('head')
<style>
    .kit-wrap{max-width:1200px;margin:0 auto;padding:clamp(40px,6vw,72px) 24px clamp(72px,10vw,120px);}
    .kit-crumbs{font-size:12px;color:#8E7F6F;margin-bottom:24px;letter-spacing:.02em;}
    .kit-crumbs a{color:#8E7F6F;text-decoration:none;transition:color .2s;}
    .kit-crumbs a:hover{color:#BE9A53;}

    .kit-layout{
        display:grid;grid-template-columns:1.05fr 1fr;gap:64px;align-items:start;
    }
    @media(max-width:900px){ .kit-layout{grid-template-columns:1fr;gap:36px;} }

    .kit-media{
        position:relative;aspect-ratio:4/3;
        background:radial-gradient(circle at 30% 25%,#FBF4E6,#E8D1C5);
        border-radius:24px;overflow:hidden;
        border:1px solid rgba(217,181,109,.20);
        box-shadow:0 40px 80px -30px rgba(190,154,83,.32);
    }
    .kit-media img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
    .kit-media__ribbon{
        position:absolute;top:20px;left:-6px;z-index:3;
        background:linear-gradient(135deg,#D9B56D 0%,#BE9A53 100%);
        color:#3B310F;
        padding:10px 20px 10px 22px;
        font:700 12px/1 'Montserrat',sans-serif;letter-spacing:.16em;text-transform:uppercase;
        border-radius:0 6px 6px 0;
        box-shadow:0 12px 24px -8px rgba(190,154,83,.55);
    }
    .kit-collage{
        position:absolute;inset:0;display:grid;
        grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;
        gap:10px;padding:24px;
    }
    .kit-collage img{
        width:100%;height:100%;object-fit:cover;border-radius:14px;background:#fff;
        box-shadow:0 8px 24px -8px rgba(46,42,38,.2);
    }

    .kit-body__eyebrow{
        font:600 11px/1 'Montserrat',sans-serif;letter-spacing:.28em;text-transform:uppercase;
        color:#BE9A53;margin-bottom:14px;
    }
    .kit-body__title{
        font-family:'Playfair Display',serif;font-weight:500;
        font-size:clamp(28px,4vw,44px);line-height:1.1;color:#2E2A26;
        margin:0 0 20px;letter-spacing:-.01em;
    }
    .kit-body__desc{
        font-size:15px;line-height:1.75;color:#6B6157;margin:0 0 28px;
    }

    .kit-items{
        border:1px solid rgba(184,169,153,.20);border-radius:16px;
        background:#FEFCF8;
        padding:8px 20px;margin-bottom:28px;
    }
    .kit-items__label{
        font:600 10px/1 'Montserrat',sans-serif;letter-spacing:.24em;text-transform:uppercase;
        color:#BE9A53;padding:14px 0 10px;border-bottom:1px solid rgba(184,169,153,.18);
    }
    .kit-items__row{
        display:flex;align-items:center;gap:14px;padding:14px 0;
        border-bottom:1px solid rgba(184,169,153,.12);
    }
    .kit-items__row:last-child{border-bottom:none;}
    .kit-items__thumb{
        width:56px;height:56px;flex-shrink:0;border-radius:10px;overflow:hidden;
        background:#FBF4E6;display:flex;align-items:center;justify-content:center;
    }
    .kit-items__thumb img{width:100%;height:100%;object-fit:cover;}
    .kit-items__thumb--ph{color:#BE9A53;font-family:'Playfair Display',serif;font-size:20px;}
    .kit-items__name{flex:1;min-width:0;}
    .kit-items__name a{color:#2E2A26;text-decoration:none;font-weight:500;font-size:14.5px;}
    .kit-items__name a:hover{color:#BE9A53;}
    .kit-items__meta{font-size:12px;color:#8E7F6F;margin-top:2px;}
    .kit-items__price{
        font-family:'Playfair Display',serif;font-weight:600;color:#2E2A26;font-size:15px;
        white-space:nowrap;
    }

    .kit-total{
        background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);
        border:1px solid rgba(217,181,109,.30);border-radius:16px;
        padding:22px 24px;margin-bottom:24px;
    }
    .kit-total__row{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;font-size:14px;color:#6B6157;}
    .kit-total__row--sep{border-top:1px dashed rgba(184,169,153,.35);margin-top:8px;padding-top:14px;}
    .kit-total__strike{text-decoration:line-through;color:#B8A999;}
    .kit-total__save{color:#C97B6B;font-weight:600;}
    .kit-total__final{
        font-family:'Playfair Display',serif;font-weight:600;color:#2E2A26;font-size:26px;
    }

    .kit-cta{
        display:flex;align-items:center;justify-content:center;gap:10px;
        width:100%;padding:18px 24px;border:none;cursor:pointer;
        background:linear-gradient(120deg,#E0BE77,#D9B56D 45%,#BE9A53);
        color:#3B310F;
        font:600 13px/1 'Montserrat',sans-serif;letter-spacing:.18em;text-transform:uppercase;
        border-radius:999px;
        box-shadow:0 18px 40px -14px rgba(190,154,83,.65);
        transition:all .35s;
    }
    .kit-cta:hover{
        transform:translateY(-2px);
        box-shadow:0 26px 50px -14px rgba(190,154,83,.85);
    }
    .kit-notice{
        margin-top:16px;font-size:12px;color:#8E7F6F;text-align:center;
        display:flex;align-items:center;justify-content:center;gap:6px;
    }
</style>
@endpush

@section('content')

<div class="kit-wrap">
    <p class="kit-crumbs">
        <a href="{{ route('home') }}">Inicio</a> · <a href="{{ route('bundles.index') }}">Kits</a> · {{ $bundle->name }}
    </p>

    <div class="kit-layout">
        <div>
            <div class="kit-media">
                @if($bundle->savings > 0)
                    <span class="kit-media__ribbon">Ahorra ${{ number_format($bundle->savings, 0, ',', '.') }} · -{{ $bundle->savings_percent }}%</span>
                @endif

                @if($bundle->image_url)
                    <img src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}">
                @elseif($bundle->items->isNotEmpty())
                    <div class="kit-collage">
                        @foreach($bundle->items->take(4) as $p)
                            @php $img = ($p->images[0] ?? null) ? asset('storage/'.$p->images[0]) : null; @endphp
                            @if($img)
                                <img src="{{ $img }}" alt="{{ $p->name }}">
                            @else
                                <div style="display:flex;align-items:center;justify-content:center;background:#FBF4E6;border-radius:14px;">
                                    <span style="font-family:'Playfair Display',serif;color:#BE9A53;font-size:26px;">{{ mb_substr($p->name, 0, 1) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            <p class="kit-body__eyebrow">— Kit Áurea</p>
            <h1 class="kit-body__title">{{ $bundle->name }}</h1>

            @if($bundle->description)
                <p class="kit-body__desc">{{ $bundle->description }}</p>
            @endif

            <div class="kit-items">
                <div class="kit-items__label">Este kit incluye</div>
                @foreach($bundle->items as $p)
                    @php
                        $qty = (int) ($p->pivot->quantity ?? 1);
                        $lineTotal = $qty * (float) $p->price;
                        $img = ($p->images[0] ?? null) ? asset('storage/'.$p->images[0]) : null;
                    @endphp
                    <div class="kit-items__row">
                        <div class="kit-items__thumb">
                            @if($img)
                                <img src="{{ $img }}" alt="{{ $p->name }}">
                            @else
                                <span class="kit-items__thumb--ph">{{ mb_substr($p->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="kit-items__name">
                            <a href="{{ route('products.show', $p->slug) }}">{{ $p->name }}</a>
                            <div class="kit-items__meta">
                                @if($qty > 1) {{ $qty }} unidades · @endif
                                ${{ number_format($p->price, 0, ',', '.') }} c/u
                            </div>
                        </div>
                        <div class="kit-items__price">${{ number_format($lineTotal, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>

            <div class="kit-total">
                <div class="kit-total__row">
                    <span>Suma individual</span>
                    <span class="kit-total__strike">${{ number_format($bundle->compare_price, 0, ',', '.') }}</span>
                </div>
                <div class="kit-total__row">
                    <span>Ahorro</span>
                    <span class="kit-total__save">-${{ number_format($bundle->savings, 0, ',', '.') }} ({{ $bundle->savings_percent }}%)</span>
                </div>
                <div class="kit-total__row kit-total__row--sep">
                    <span style="color:#2E2A26;font-weight:500;">Precio del kit</span>
                    <span class="kit-total__final">${{ number_format($bundle->price, 0, ',', '.') }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('bundles.add', $bundle->slug) }}">
                @csrf
                <button type="submit" class="kit-cta">
                    Agregar el kit al carrito ✨
                </button>
            </form>

            <div class="kit-notice">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                El descuento se aplica automáticamente al agregar el kit
            </div>
        </div>
    </div>
</div>

@endsection
