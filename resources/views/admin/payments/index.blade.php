@extends('layouts.admin')

@section('title', 'Pasarela ePayco')
@section('page_title', 'Pasarela ePayco')

@section('content')
<div class="max-w-2xl mx-auto"
     x-data="{
        showPrivate: false,
        showPKey: false,
        test: @js($epayco_test),
     }">

    @php
        // Los datos bancarios los edita el admin en otra pantalla, pero el impacto se ve aquí:
        // si el número de cuenta está vacío, el checkout oculta la opción "Transferencia" y
        // los correos/tracking no pueden mostrar los datos. Avisamos con banner rojo prominente.
        $__bankAccountLoaded = ! empty(trim((string) \App\Models\BankTransferSetting::get('account_number', '')));
    @endphp
    @unless($__bankAccountLoaded)
    <div class="bg-red-50 border-2 border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm mb-5 flex items-start gap-2">
        <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
        </svg>
        <div>
            <p class="font-bold">⚠️ Datos bancarios incompletos — no aparece el método a los clientes</p>
            <p class="mt-1 text-red-700">
                El número de cuenta bancaria está vacío, por lo que la opción <strong>Transferencia</strong> está oculta en el checkout.
                Completa los datos en
                <a href="{{ route('admin.bank-transfer.index') }}" class="underline font-semibold text-red-800 hover:text-red-900">Configuración → Transferencia bancaria</a>.
            </p>
        </div>
    </div>
    @endunless

    <p class="text-sm text-gray-500 mb-6">
        Configura las llaves de ePayco que usa el checkout para cobrar. Obtén tus credenciales en
        <a href="https://dashboard.epayco.com/" target="_blank" rel="noopener" class="text-blue-600 hover:underline">dashboard.epayco.com</a>
        &rarr; Configuración &rarr; Llaves API.
        Estas llaves reemplazan a las del archivo <code class="bg-gray-100 px-1 rounded">.env</code>.
    </p>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-5 flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.payments.update') }}">
        @method('PUT')
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Indicador de modo (Test / Live) --}}
            <div class="flex items-center justify-between border border-gray-200 rounded-lg p-3 bg-gray-50">
                <div>
                    <div class="text-sm font-medium text-gray-700">Modo de operación</div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <span x-show="test">Estás en <strong>Sandbox (pruebas)</strong>: no se cobra dinero real.</span>
                        <span x-show="!test">Estás en <strong>Producción (Live)</strong>: los cobros son reales.</span>
                    </p>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="epayco_test" x-bind:value="test ? 1 : 0">
                    <button type="button"
                            @click="test = !test"
                            :class="test ? 'bg-amber-500' : 'bg-green-600'"
                            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors">
                        <span class="sr-only">Modo</span>
                        <span :class="test ? 'translate-x-1' : 'translate-x-6'"
                              class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"></span>
                    </button>
                    <span class="text-xs font-semibold"
                          :class="test ? 'text-amber-700' : 'text-green-700'"
                          x-text="test ? 'Test' : 'Live'"></span>
                </label>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-800 px-3 py-2 text-xs">
                Cuando cambies a producción, marca <strong>Live</strong> y pega tus llaves de producción del panel ePayco (las de sandbox no cobran real).
            </div>

            {{-- Clave pública --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                <input type="text" name="epayco_public_key" value="{{ $epayco_public_key }}"
                       placeholder="Ej. 04b1c6a1..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                <p class="text-xs text-gray-400 mt-1">Visible en el frontend (para inicializar el widget Onpage). No es secreta.</p>
            </div>

            {{-- Private key --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Private Key</label>
                <div class="relative">
                    <input :type="showPrivate ? 'text' : 'password'" name="epayco_private_key" value="{{ $epayco_private_key }}"
                           placeholder="Tu Private Key de ePayco"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono pr-11">
                    <button type="button" @click="showPrivate = !showPrivate"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg x-show="!showPrivate" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg x-show="showPrivate" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1"><strong>Confidencial</strong>: se usa desde el servidor para autenticar peticiones a la API.</p>
            </div>

            {{-- P_CUST_ID --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">P_CUST_ID_CLIENTE</label>
                <input type="text" name="epayco_p_cust_id" value="{{ $epayco_p_cust_id }}"
                       placeholder="Ej. 123456"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                <p class="text-xs text-gray-400 mt-1">Identificador de tu cuenta en ePayco (aparece en el panel).</p>
            </div>

            {{-- P_KEY --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">P_KEY (firma del webhook)</label>
                <div class="relative">
                    <input :type="showPKey ? 'text' : 'password'" name="epayco_p_key" value="{{ $epayco_p_key }}"
                           placeholder="Tu P_KEY de ePayco"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono pr-11">
                    <button type="button" @click="showPKey = !showPKey"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg x-show="!showPKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg x-show="showPKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    <strong>Confidencial</strong>. Se usa para validar la firma del webhook (URL de confirmación en ePayco):
                    <code class="bg-gray-100 px-1 rounded break-all">{{ url('/epayco/webhook') }}</code>
                </p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </div>
    </form>

    <p class="text-xs text-gray-400 mt-4">
        Consejo: mientras pruebas, deja <strong>Test</strong> activo (sandbox de ePayco). Cuando salgas a producción, cambia a <strong>Live</strong> y pega las llaves reales.
    </p>
</div>
@endsection
