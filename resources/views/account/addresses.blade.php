@extends('layouts.app')

@section('title', 'Mis direcciones | Belleza Áurea')
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

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3.5vw,32px);font-weight:700;color:#2E2A26;margin:0 0 22px;">Mis direcciones</h1>

            @php
                $inputStyle = 'width:100%;color:#2E2A26;background:#fff;border:1px solid #E5DCC9;border-radius:12px;padding:11px 14px;font-size:15px;outline:none;';
                $labelStyle = 'display:block;font-size:13px;font-weight:600;color:#4B4541;margin-bottom:6px;';
            @endphp

            {{-- Existing addresses --}}
            @if($addresses->isEmpty())
                <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(20px,3.5vw,32px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);margin-bottom:26px;text-align:center;color:#6B6157;font-size:15px;">
                    Aún no tienes direcciones guardadas.
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:26px;">
                    @foreach($addresses as $address)
                        <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(18px,3vw,24px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:10px;">
                                <span style="font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:#2E2A26;">{{ $address->label ?: 'Dirección' }}</span>
                                @if($address->is_default)
                                    <span style="display:inline-block;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#8A6E2E;background:linear-gradient(120deg,rgba(224,190,119,.28),rgba(217,181,109,.16));border:1px solid #E0BE77;border-radius:9999px;padding:3px 11px;">Predeterminada</span>
                                @endif
                            </div>

                            @if($address->recipient)
                                <p style="margin:0 0 4px;color:#2E2A26;font-size:15px;font-weight:600;">{{ $address->recipient }}</p>
                            @endif
                            <p style="margin:0 0 4px;color:#6B6157;font-size:14px;line-height:1.55;">{{ $address->one_line }}</p>
                            @if($address->phone)
                                <p style="margin:0;color:#6B6157;font-size:14px;">Tel: {{ $address->phone }}</p>
                            @endif

                            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:16px;">
                                @unless($address->is_default)
                                    <form method="POST" action="{{ route('account.addresses.default', $address) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                style="padding:9px 18px;border:1px solid #E0BE77;border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#8A6E2E;background:rgba(224,190,119,.10);transition:background .2s;"
                                                onmouseover="this.style.background='rgba(224,190,119,.22)'"
                                                onmouseout="this.style.background='rgba(224,190,119,.10)'">
                                            Hacer predeterminada
                                        </button>
                                    </form>
                                @endunless

                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}"
                                      onsubmit="return confirm('¿Eliminar esta dirección?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="padding:9px 18px;border:1px solid #E5C3BB;border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#C97B6B;background:rgba(201,123,107,.06);transition:background .2s;"
                                            onmouseover="this.style.background='rgba(201,123,107,.14)'"
                                            onmouseout="this.style.background='rgba(201,123,107,.06)'">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add address --}}
            <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(20px,3.5vw,32px);box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                <h2 style="font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:#2E2A26;margin:0 0 18px;">Agregar dirección</h2>

                <form method="POST" action="{{ route('account.addresses.store') }}" style="display:flex;flex-direction:column;gap:16px;">
                    @csrf

                    <div style="display:flex;flex-wrap:wrap;gap:16px;">
                        <div style="flex:1 1 160px;min-width:0;">
                            <label for="label" style="{{ $labelStyle }}">Etiqueta</label>
                            <input type="text" id="label" name="label" value="{{ old('label') }}" placeholder="Casa, Oficina..." style="{{ $inputStyle }}">
                            @error('label') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                        <div style="flex:1 1 160px;min-width:0;">
                            <label for="recipient" style="{{ $labelStyle }}">Quien recibe</label>
                            <input type="text" id="recipient" name="recipient" value="{{ old('recipient') }}" placeholder="Nombre de quien recibe" style="{{ $inputStyle }}">
                            @error('recipient') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="phone" style="{{ $labelStyle }}">Teléfono</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" style="{{ $inputStyle }}">
                        @error('phone') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address" style="{{ $labelStyle }}">Dirección</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" required style="{{ $inputStyle }}">
                        @error('address') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:16px;">
                        <div style="flex:1 1 160px;min-width:0;">
                            <label for="city" style="{{ $labelStyle }}">Ciudad</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}" style="{{ $inputStyle }}">
                            @error('city') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                        <div style="flex:1 1 160px;min-width:0;">
                            <label for="state" style="{{ $labelStyle }}">Departamento</label>
                            <input type="text" id="state" name="state" value="{{ old('state') }}" required style="{{ $inputStyle }}">
                            @error('state') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                        <div style="flex:1 1 120px;min-width:0;">
                            <label for="zip_code" style="{{ $labelStyle }}">Código postal</label>
                            <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" style="{{ $inputStyle }}">
                            @error('zip_code') <p style="color:#C97B6B;font-size:13px;margin:6px 0 0;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:9px;">
                        <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:#BE9A53;">
                        <label for="is_default" style="font-size:14px;color:#6B6157;">Usar como predeterminada</label>
                    </div>
                    @error('is_default') <p style="color:#C97B6B;font-size:13px;margin:-8px 0 0;">{{ $message }}</p> @enderror

                    <button type="submit"
                            style="margin-top:8px;align-self:flex-start;padding:13px 34px;border:none;border-radius:9999px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:15px;font-weight:600;letter-spacing:.02em;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);transition:filter .2s,transform .2s;"
                            onmouseover="this.style.filter='brightness(1.04)';this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.filter='none';this.style.transform='none'">
                        Guardar dirección
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
