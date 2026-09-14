@extends('layouts.app')

@section('title', 'Crear cuenta | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
<section style="padding:clamp(40px,7vw,80px) 20px;background:#FBF8F2;">
    <div style="max-width:440px;margin:0 auto;">

        <div style="text-align:center;margin-bottom:26px;">
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(26px,4vw,34px);font-weight:700;color:#2E2A26;margin:0;">Crear cuenta</h1>
            <p style="margin:10px 0 0;color:#6B6157;font-size:15px;">Únete a Belleza Áurea y sigue tus pedidos.</p>
        </div>

        @isset($referrer)
            @if($referrer)
                <div style="background:linear-gradient(135deg,#FFF8E8,#F6E6C0 55%,#EBCF90);border:1px solid #E0BE77;border-radius:14px;padding:14px 16px;margin-bottom:18px;color:#7A5E1C;font-size:14px;text-align:center;">
                    💛 Vienes referida por <strong>{{ $referrer->name ?: 'una amiga' }}</strong> — al hacer tu primera compra recibirás <strong>500 puntos de bienvenida</strong>.
                </div>
            @endif
        @endisset

        <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:20px;padding:clamp(24px,4vw,34px);box-shadow:0 28px 60px -40px rgba(120,92,44,.5);">

            @if(session('success'))
                <div style="background:#EAF3EA;border:1px solid #C5E0C5;color:#3B7A3B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="background:#F8E9E6;border:1px solid #E5C3BB;color:#C97B6B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('customer.register.submit') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf

                <div>
                    <label for="name" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Nombre completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('name') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('email') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Teléfono <span style="color:#9A8F82;font-weight:400;">(opcional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('phone') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('password') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('password_confirmation') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div style="display:flex;align-items:flex-start;gap:9px;">
                    <input type="checkbox" id="habeas_data" name="habeas_data" value="1" required {{ old('habeas_data') ? 'checked' : '' }}
                           style="margin-top:3px;width:16px;height:16px;accent-color:#BE9A53;flex-shrink:0;">
                    <label for="habeas_data" style="font-size:13px;color:#6B6157;line-height:1.55;">
                        Acepto la <a href="{{ route('legal.privacy') }}" target="_blank" style="color:#BE9A53;font-weight:600;text-decoration:underline;">política de tratamiento de datos</a> (Ley 1581 de 2012).
                    </label>
                </div>
                @error('habeas_data') <p style="color:#C97B6B;font-size:13px;margin:-8px 0 0;">{{ $message }}</p> @enderror

                <button type="submit"
                        style="margin-top:6px;width:100%;padding:13px;border:none;border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:15px;font-weight:600;letter-spacing:.02em;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);transition:filter .2s,transform .2s;"
                        onmouseover="this.style.filter='brightness(1.04)';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.filter='none';this.style.transform='none'">
                    Crear cuenta
                </button>
            </form>
        </div>

        <p style="text-align:center;margin-top:20px;color:#6B6157;font-size:14px;">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color:#BE9A53;font-weight:600;text-decoration:none;">Inicia sesión</a>
        </p>
    </div>
</section>
@endsection
