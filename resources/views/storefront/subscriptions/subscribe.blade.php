@extends('layouts.app')

@section('title', 'Suscribirme a ' . $plan->name)

@push('head')
<style>
    .sub-wrap{max-width:960px;margin:0 auto;padding:clamp(48px,7vw,88px) 24px;display:grid;grid-template-columns:1fr 340px;gap:36px;}
    @media(max-width:820px){.sub-wrap{grid-template-columns:1fr;}}

    .sub-form{background:#fff;border:1px solid rgba(217,181,109,.22);border-radius:22px;padding:32px 28px;}
    .sub-form h1{font-family:'Playfair Display',serif;font-size:clamp(24px,3vw,32px);color:#2E2A26;margin:0 0 8px;}
    .sub-form .lede{color:#6B6157;margin:0 0 24px;font-size:14px;line-height:1.6;}
    .sub-form label{display:block;font:600 12px/1 'Montserrat',sans-serif;text-transform:uppercase;letter-spacing:.14em;color:#8B7B5C;margin:14px 0 6px;}
    .sub-form input,.sub-form select,.sub-form textarea{
        width:100%;padding:12px 14px;border:1px solid rgba(217,181,109,.35);border-radius:10px;
        background:#FBF8F2;font-size:15px;color:#2E2A26;font-family:inherit;
    }
    .sub-form input:focus{outline:none;border-color:#D9B56D;box-shadow:0 0 0 3px rgba(217,181,109,.15);}
    .sub-form .row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    @media(max-width:520px){.sub-form .row{grid-template-columns:1fr;}}
    .sub-form .pay{display:flex;flex-direction:column;gap:8px;margin-top:8px;}
    .sub-form .pay label.opt{
        display:flex;gap:10px;align-items:center;padding:14px;border:1px solid rgba(217,181,109,.35);
        border-radius:10px;cursor:pointer;font:500 14px/1.3 'Montserrat',sans-serif;
        color:#2E2A26;text-transform:none;letter-spacing:0;margin:0;
    }
    .sub-form .pay input:checked + span{font-weight:700;}
    .sub-form button{
        margin-top:24px;width:100%;background:#2E2A26;color:#FBF8F2;
        border:none;border-radius:999px;padding:16px;font:600 13px/1 'Montserrat',sans-serif;
        letter-spacing:.16em;text-transform:uppercase;cursor:pointer;transition:background .3s;
    }
    .sub-form button:hover{background:#D9B56D;color:#3B310F;}

    .sub-summary{
        background:linear-gradient(160deg,#FEFCF8 0%,#F8F2E8 100%);
        border:1px solid rgba(217,181,109,.22);border-radius:22px;padding:28px 24px;height:fit-content;position:sticky;top:100px;
    }
    .sub-summary h2{font-family:'Playfair Display',serif;font-size:22px;color:#2E2A26;margin:0 0 4px;}
    .sub-summary .interval{font-size:13px;color:#8B7B5C;margin-bottom:18px;}
    .sub-summary ul{margin:0 0 18px;padding:0;list-style:none;}
    .sub-summary li{font-size:13px;color:#3B310F;padding:5px 0;display:flex;gap:8px;}
    .sub-summary li::before{content:"✓";color:#D9B56D;font-weight:700;}
    .sub-summary .totals{border-top:1px dashed rgba(139,123,92,.4);padding-top:14px;font-size:14px;color:#6B6157;}
    .sub-summary .totals strong{font-family:'Playfair Display',serif;font-size:26px;color:#2E2A26;}
    .sub-summary s{color:#B4A99A;}
    .sub-summary .off{background:#FBF6EC;color:#BE9A53;font-weight:700;font-size:11px;padding:4px 10px;border-radius:999px;letter-spacing:.14em;text-transform:uppercase;}

    .field-err{color:#B00020;font-size:12px;margin-top:4px;}
</style>
@endpush

@section('content')
@php
    $prefill = $default ?? $customer;
    $off = $plan->computedDiscountPercent();
@endphp
<div class="sub-wrap">
    <form class="sub-form" method="POST" action="{{ route('subscriptions.store', $plan) }}">
        @csrf
        <h1>Un paso para comenzar tu ritual 🌸</h1>
        <p class="lede">Confirma tus datos de entrega y elige cómo prefieres pagar. Podrás pausar o cancelar cuando quieras desde Mi cuenta.</p>

        <label>Nombre completo</label>
        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required>
        @error('name')<div class="field-err">{{ $message }}</div>@enderror

        <label>Teléfono</label>
        <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}">

        <label>Dirección</label>
        <input type="text" name="address" value="{{ old('address', $prefill->address ?? '') }}" required>
        @error('address')<div class="field-err">{{ $message }}</div>@enderror

        <div class="row">
            <div>
                <label>Ciudad</label>
                <input type="text" name="city" value="{{ old('city', $prefill->city ?? '') }}" required>
                @error('city')<div class="field-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label>Departamento</label>
                <input type="text" name="state" value="{{ old('state', $prefill->state ?? '') }}" required>
                @error('state')<div class="field-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <label>Código postal</label>
        <input type="text" name="zip_code" value="{{ old('zip_code', $prefill->zip_code ?? '') }}">

        <label>Método de pago</label>
        <div class="pay">
            <label class="opt">
                <input type="radio" name="payment_method" value="epayco" {{ old('payment_method','epayco') === 'epayco' ? 'checked' : '' }}>
                <span>ePayco — pago recurrente automático</span>
            </label>
            <label class="opt">
                <input type="radio" name="payment_method" value="cash_on_delivery" {{ old('payment_method') === 'cash_on_delivery' ? 'checked' : '' }}>
                <span>Contra entrega — pagas al recibir</span>
            </label>
        </div>
        @error('payment_method')<div class="field-err">{{ $message }}</div>@enderror

        <button type="submit">Confirmar mi ritual</button>
    </form>

    <aside class="sub-summary">
        <h2>{{ $plan->name }}</h2>
        <p class="interval">Cada {{ $plan->interval_days }} días @if($plan->delivery_days_message) · {{ $plan->delivery_days_message }} @endif</p>

        <ul>
            @foreach($plan->products as $p)
                <li>{{ $p->name }} @if($p->pivot->quantity > 1)<span style="color:#8B7B5C;">× {{ $p->pivot->quantity }}</span>@endif</li>
            @endforeach
        </ul>

        <div class="totals">
            @if($off > 0)<p style="margin:0 0 6px;"><span class="off">-{{ $off }}%</span></p>@endif
            <p style="margin:0;">
                <strong>${{ number_format((float) $plan->base_price, 0, ',', '.') }}</strong>
                @if((float) $plan->regular_price > (float) $plan->base_price)
                    <s style="margin-left:8px;">${{ number_format((float) $plan->regular_price, 0, ',', '.') }}</s>
                @endif
            </p>
            <p style="font-size:12px;margin:8px 0 0;color:#8B7B5C;">
                Primera entrega: <strong>{{ now()->addDays((int) $plan->interval_days)->translatedFormat('l j \d\e F') }}</strong>
            </p>
        </div>
    </aside>
</div>
@endsection
