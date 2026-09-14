@extends('layouts.app')

@section('title', 'Programa mayorista | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@php
    $discountPct = (int) round((1 - (float) config('wholesale.default_discount', 0.80)) * 100);
    $status = $customer->wholesaler_status;
@endphp

@section('content')
<section style="padding:clamp(32px,5vw,60px) 20px;background:#FBF8F2;min-height:60vh;">
    <div style="max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap;gap:26px;align-items:flex-start;">

        {{-- Sidebar --}}
        <div style="flex:1 1 220px;min-width:220px;max-width:280px;">
            @include('account._nav')
        </div>

        {{-- Main --}}
        <div style="flex:3 1 480px;min-width:0;">

            @if(session('success'))
                <div style="background:#EAF3EA;border:1px solid #C5E0C5;color:#3B7A3B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div style="background:#F3EEE0;border:1px solid #E5DCC9;color:#6B6157;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">
                    {{ session('info') }}
                </div>
            @endif

            {{-- Hero card --}}
            <div style="background:linear-gradient(135deg,#FFF8E8,#F6E6C0 55%,#EBCF90);border:1px solid #E0BE77;border-radius:20px;padding:clamp(24px,4vw,38px);box-shadow:0 22px 52px -34px rgba(190,154,83,.7);">
                <p style="margin:0;font-family:'Montserrat',sans-serif;font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">
                    Programa Áurea Mayoristas
                </p>
                <h1 style="margin:8px 0 10px;font-family:'Playfair Display',serif;font-size:clamp(26px,3.8vw,34px);font-weight:700;color:#3B310F;line-height:1.15;">
                    Únete al programa mayorista Áurea 💛
                </h1>
                <p style="margin:0;color:#5C4A20;font-size:15px;line-height:1.55;max-width:60ch;">
                    Precios preferenciales, envíos priorizados y atención directa para tu tienda,
                    salón o distribuidora. Pensado para quienes hacen crecer la belleza consciente en Colombia.
                </p>

                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:22px;">
                    <div style="background:rgba(255,255,255,.7);border:1px solid rgba(190,154,83,.35);border-radius:14px;padding:12px 16px;min-width:170px;flex:1 1 170px;">
                        <p style="margin:0;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">Descuento</p>
                        <p style="margin:4px 0 0;font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#3B310F;">Hasta {{ $discountPct }}%</p>
                        <p style="margin:2px 0 0;font-size:12px;color:#6B6157;">Sobre precio público</p>
                    </div>
                    <div style="background:rgba(255,255,255,.7);border:1px solid rgba(190,154,83,.35);border-radius:14px;padding:12px 16px;min-width:170px;flex:1 1 170px;">
                        <p style="margin:0;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">Envío</p>
                        <p style="margin:4px 0 0;font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#3B310F;">Preferencial</p>
                        <p style="margin:2px 0 0;font-size:12px;color:#6B6157;">Nacional</p>
                    </div>
                    <div style="background:rgba(255,255,255,.7);border:1px solid rgba(190,154,83,.35);border-radius:14px;padding:12px 16px;min-width:170px;flex:1 1 170px;">
                        <p style="margin:0;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#8A6E2E;font-weight:700;">Atención</p>
                        <p style="margin:4px 0 0;font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#3B310F;">Directa</p>
                        <p style="margin:2px 0 0;font-size:12px;color:#6B6157;">WhatsApp exclusivo</p>
                    </div>
                </div>
            </div>

            {{-- Estado / Formulario --}}
            @if($status === 'approved')
                <div style="margin-top:22px;background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(20px,3vw,28px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                        <span style="display:inline-block;padding:6px 14px;border-radius:9999px;font-size:12px;font-weight:700;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);letter-spacing:.06em;">
                            ✨ Mayorista aprobada
                        </span>
                    </div>
                    <h2 style="margin:0 0 6px;font-family:'Playfair Display',serif;font-size:22px;color:#2E2A26;">
                        {{ $customer->wholesaler_company_name ?? 'Tu cuenta' }} está activa
                    </h2>
                    <p style="margin:0;color:#6B6157;font-size:14px;line-height:1.55;">
                        Los precios mayoristas ya se aplican automáticamente en el catálogo y el carrito.
                        En cada producto verás el precio preferencial y el público tachado.
                    </p>
                    <div style="margin-top:20px;">
                        <a href="{{ route('products.index') }}"
                           style="display:inline-block;padding:12px 26px;border-radius:9999px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);text-decoration:none;box-shadow:0 12px 26px -14px rgba(190,154,83,.9);">
                            Ver catálogo con mis precios →
                        </a>
                    </div>
                </div>

            @elseif($status === 'pending')
                <div style="margin-top:22px;background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(20px,3vw,28px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                    <span style="display:inline-block;padding:5px 12px;border-radius:9999px;font-size:12px;font-weight:700;color:#8A6D1F;background:#FBF0D5;">
                        En revisión ⏳
                    </span>
                    <h2 style="margin:12px 0 6px;font-family:'Playfair Display',serif;font-size:20px;color:#2E2A26;">
                        Tu solicitud está en revisión
                    </h2>
                    <p style="margin:0;color:#6B6157;font-size:14px;line-height:1.55;">
                        Te contactamos pronto (usualmente en menos de 24 h hábiles) al correo
                        <strong style="color:#2E2A26;">{{ $customer->email }}</strong> o al teléfono
                        <strong style="color:#2E2A26;">{{ $customer->phone ?? '—' }}</strong>.
                    </p>
                    @if($customer->wholesaler_requested_at)
                        <p style="margin:10px 0 0;color:#8A6E2E;font-size:12px;">
                            Enviada el {{ $customer->wholesaler_requested_at->format('d/m/Y') }}.
                        </p>
                    @endif
                </div>

            @else
                {{-- status = none o rejected → mostrar el formulario. --}}
                @if($status === 'rejected')
                    <div style="margin-top:22px;background:#F8E9E6;border:1px solid #E5C3BB;border-radius:14px;padding:16px 18px;">
                        <p style="margin:0 0 6px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;font-weight:700;color:#8A4237;">Solicitud anterior</p>
                        <p style="margin:0;color:#5C2E28;font-size:14px;line-height:1.55;white-space:pre-wrap;">
                            {{ $customer->wholesaler_notes ?: 'No pudimos aprobar tu solicitud anterior. Puedes actualizarla y volver a enviarla abajo.' }}
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('account.wholesale.submit') }}"
                      style="margin-top:22px;background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(20px,3vw,28px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                    @csrf

                    <h2 style="margin:0 0 4px;font-family:'Playfair Display',serif;font-size:22px;font-weight:600;color:#2E2A26;">
                        Cuéntanos sobre tu negocio
                    </h2>
                    <p style="margin:0 0 20px;color:#6B6157;font-size:14px;">
                        Todos los campos con * son obligatorios.
                    </p>

                    @if($errors->any())
                        <div style="background:#F8E9E6;border:1px solid #E5C3BB;color:#8A4237;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">
                            <ul style="margin:0;padding-left:18px;">
                                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $inputStyle = 'width:100%;padding:11px 14px;border:1px solid #E5DCC9;border-radius:10px;font-family:inherit;font-size:14px;color:#2E2A26;background:#FFFFFF;';
                        $labelStyle = 'display:block;font-family:"Montserrat",sans-serif;font-size:12px;font-weight:600;letter-spacing:.06em;color:#8A6E2E;text-transform:uppercase;margin-bottom:6px;';
                    @endphp

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
                        <div>
                            <label for="wholesaler_company_name" style="{{ $labelStyle }}">Razón social *</label>
                            <input id="wholesaler_company_name" name="wholesaler_company_name" type="text" required maxlength="200"
                                   value="{{ old('wholesaler_company_name', $customer->wholesaler_company_name) }}"
                                   style="{{ $inputStyle }}">
                        </div>
                        <div>
                            <label for="wholesaler_nit" style="{{ $labelStyle }}">NIT / documento *</label>
                            <input id="wholesaler_nit" name="wholesaler_nit" type="text" required maxlength="40"
                                   value="{{ old('wholesaler_nit', $customer->wholesaler_nit) }}"
                                   style="{{ $inputStyle }}">
                        </div>
                        <div>
                            <label for="city" style="{{ $labelStyle }}">Ciudad *</label>
                            <input id="city" name="city" type="text" required maxlength="120"
                                   value="{{ old('city', $customer->city) }}"
                                   style="{{ $inputStyle }}">
                        </div>
                        <div>
                            <label for="phone" style="{{ $labelStyle }}">Teléfono comercial *</label>
                            <input id="phone" name="phone" type="tel" required maxlength="30"
                                   value="{{ old('phone', $customer->phone) }}"
                                   style="{{ $inputStyle }}">
                        </div>
                        <div style="grid-column:1/-1;">
                            <label for="wholesaler_monthly_volume" style="{{ $labelStyle }}">Volumen mensual estimado *</label>
                            <select id="wholesaler_monthly_volume" name="wholesaler_monthly_volume" required
                                    style="{{ $inputStyle }} appearance:auto;">
                                <option value="">Elige un rango</option>
                                @foreach($volumeOptions as $val => $label)
                                    <option value="{{ $val }}" @selected(old('wholesaler_monthly_volume', $customer->wholesaler_monthly_volume) === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="grid-column:1/-1;">
                            <label for="wholesaler_notes" style="{{ $labelStyle }}">Comentarios (opcional)</label>
                            <textarea id="wholesaler_notes" name="wholesaler_notes" rows="4" maxlength="1500"
                                      placeholder="Cuéntanos sobre tu tienda, hace cuánto operas, marcas que ya manejas…"
                                      style="{{ $inputStyle }} resize:vertical;">{{ old('wholesaler_notes') }}</textarea>
                        </div>
                    </div>

                    <button type="submit"
                            style="margin-top:22px;display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border:none;border-radius:9999px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 14px 30px -14px rgba(190,154,83,.9);cursor:pointer;">
                        Enviar solicitud →
                    </button>
                    <p style="margin:12px 0 0;font-size:12px;color:#8A6E2E;">
                        Al enviar aceptas que te contactemos por correo o WhatsApp para completar el proceso.
                    </p>
                </form>
            @endif
        </div>
    </div>
</section>
@endsection
