@extends('layouts.app')

@section('title', 'Restablecer contraseña | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
<section style="padding:clamp(40px,7vw,80px) 20px;background:#FBF8F2;">
    <div style="max-width:440px;margin:0 auto;">

        <div style="text-align:center;margin-bottom:26px;">
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(26px,4vw,34px);font-weight:700;color:#2E2A26;margin:0;">Nueva contraseña</h1>
            <p style="margin:10px 0 0;color:#6B6157;font-size:15px;">Elige una nueva contraseña para tu cuenta.</p>
        </div>

        <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:20px;padding:clamp(24px,4vw,34px);box-shadow:0 28px 60px -40px rgba(120,92,44,.5);">

            @if(session('success'))
                <div style="background:#EAF3EA;border:1px solid #C5E0C5;color:#3B7A3B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="background:#F8E9E6;border:1px solid #E5C3BB;color:#C97B6B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('customer.password.update') }}" style="display:flex;flex-direction:column;gap:16px;">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ $email }}" required readonly
                           style="width:100%;color:#9A8F82;background:#F7F3ED;border:1px solid #EFE7D8;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('email') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Nueva contraseña</label>
                    <input type="password" id="password" name="password" required autofocus autocomplete="new-password"
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('password') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" style="display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                           style="width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;">
                    @error('password_confirmation') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        style="margin-top:6px;width:100%;padding:13px;border:none;border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:15px;font-weight:600;letter-spacing:.02em;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);transition:filter .2s,transform .2s;"
                        onmouseover="this.style.filter='brightness(1.04)';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.filter='none';this.style.transform='none'">
                    Restablecer contraseña
                </button>
            </form>
        </div>

        <p style="text-align:center;margin-top:20px;color:#6B6157;font-size:14px;">
            <a href="{{ route('customer.login') }}" style="color:#BE9A53;font-weight:600;text-decoration:none;">Volver a iniciar sesión</a>
        </p>
    </div>
</section>
@endsection
