@extends('layouts.app')

@section('title', 'Mis datos | Belleza Áurea')
@section('robots', 'noindex, nofollow')

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
                <div style="background:#EAF3EA;border:1px solid #C5E0C5;color:#3B7A3B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="background:#F8E9E6;border:1px solid #E5C3BB;color:#C97B6B;padding:12px 14px;border-radius:12px;font-size:14px;margin-bottom:18px;">{{ session('error') }}</div>
            @endif

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3.5vw,32px);font-weight:700;color:#2E2A26;margin:0 0 22px;">Mis datos</h1>

            <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(20px,3.5vw,32px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">

                <form method="POST" action="{{ route('account.profile.update') }}" style="display:flex;flex-direction:column;gap:16px;">
                    @csrf
                    @method('PUT')

                    @php
                        $inputStyle = 'width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;';
                        $labelStyle = 'display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;';
                    @endphp

                    <div>
                        <label style="{{ $labelStyle }}">Correo electrónico</label>
                        <input type="email" value="{{ $customer->email }}" disabled
                               style="width:100%;color:#9A8F82;background:#F7F3ED;border:1px solid #EFE7D8;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;cursor:not-allowed;">
                        <p style="margin:6px 0 0;color:#9A8F82;font-size:12px;">El correo no se puede modificar.</p>
                    </div>

                    <div>
                        <label for="name" style="{{ $labelStyle }}">Nombre completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required style="{{ $inputStyle }}">
                        @error('name') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" style="{{ $labelStyle }}">Teléfono</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" style="{{ $inputStyle }}">
                        @error('phone') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address" style="{{ $labelStyle }}">Dirección</label>
                        <input type="text" id="address" name="address" value="{{ old('address', $customer->address) }}" style="{{ $inputStyle }}">
                        @error('address') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:16px;">
                        <div style="flex:1 1 160px;min-width:0;">
                            <label for="city" style="{{ $labelStyle }}">Ciudad</label>
                            <input type="text" id="city" name="city" value="{{ old('city', $customer->city) }}" style="{{ $inputStyle }}">
                            @error('city') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                        <div style="flex:1 1 160px;min-width:0;">
                            <label for="state" style="{{ $labelStyle }}">Departamento</label>
                            <input type="text" id="state" name="state" value="{{ old('state', $customer->state) }}" style="{{ $inputStyle }}">
                            @error('state') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                        <div style="flex:1 1 120px;min-width:0;">
                            <label for="zip_code" style="{{ $labelStyle }}">Código postal</label>
                            <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', $customer->zip_code) }}" style="{{ $inputStyle }}">
                            @error('zip_code') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div style="margin-top:8px;padding-top:20px;border-top:1px solid #EFE7D8;">
                        <h2 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:#2E2A26;margin:0 0 4px;">Cambiar contraseña</h2>
                        <p style="margin:0 0 16px;color:#6B6157;font-size:13px;">Déjalo en blanco si no deseas cambiarla.</p>

                        <div style="display:flex;flex-direction:column;gap:16px;">
                            <div>
                                <label for="password" style="{{ $labelStyle }}">Nueva contraseña (opcional)</label>
                                <input type="password" id="password" name="password" autocomplete="new-password" style="{{ $inputStyle }}">
                                @error('password') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" style="{{ $labelStyle }}">Confirmar nueva contraseña</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" style="{{ $inputStyle }}">
                                @error('password_confirmation') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            style="margin-top:8px;align-self:flex-start;padding:13px 34px;border:none;border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:15px;font-weight:600;letter-spacing:.02em;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);transition:filter .2s,transform .2s;"
                            onmouseover="this.style.filter='brightness(1.04)';this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.filter='none';this.style.transform='none'">
                        Guardar cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
