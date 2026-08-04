@extends('layouts.app')

@section('title', 'Iniciar sesión | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
<section style="padding:clamp(40px,7vw,80px) 20px;background:#FBF8F2;">
    <div style="max-width:440px;margin:0 auto;">

        <div style="text-align:center;margin-bottom:26px;">
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(26px,4vw,34px);font-weight:700;color:#2E2A26;margin:0;">Iniciar sesión</h1>
            <p style="margin:10px 0 0;color:#6B6157;font-size:15px;">Bienvenida de nuevo a Belleza Áurea.</p>
        </div>

        <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:20px;padding:clamp(24px,4vw,34px);box-shadow:0 28px 60px -40px rgba(120,92,44,.5);">

            @if(session('success'))
                <div style="background:#EAF3EA;border:1px solid #C5E0C5;color:#3B7A3B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="background:#F8E9E6;border:1px solid #E5C3BB;color:#C97B6B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('customer.login.submit') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf

                <div>
                    <label for="email" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('email') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('password') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div style="display:flex;align-items:center;gap:9px;">
                    <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:#BE9A53;">
                    <label for="remember" style="font-size:14px;color:#6B6157;">Recordarme</label>
                </div>

                <button type="submit"
                        style="margin-top:6px;width:100%;padding:13px;border:none;border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:15px;font-weight:600;letter-spacing:.02em;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);transition:filter .2s,transform .2s;"
                        onmouseover="this.style.filter='brightness(1.04)';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.filter='none';this.style.transform='none'">
                    Ingresar
                </button>

                <p style="text-align:center;margin:2px 0 0;font-size:14px;">
                    <a href="{{ route('customer.password.request') }}" style="color:#BE9A53;font-weight:600;text-decoration:none;">¿Olvidaste tu contraseña?</a>
                </p>
            </form>
        </div>

        <p style="text-align:center;margin-top:20px;color:#6B6157;font-size:14px;">
            ¿No tienes cuenta? <a href="{{ route('customer.register') }}" style="color:#BE9A53;font-weight:600;text-decoration:none;">Regístrate</a>
        </p>
    </div>
</section>
@endsection
