@extends('layouts.admin')

@section('title', 'Pasarela de pago')
@section('page_title', 'Pasarela de pago (Stripe)')

@section('content')
<div class="max-w-2xl mx-auto"
     x-data="{
        showSecret: false,
        showWebhook: false,
        pk: @js($stripeKey),
        get mode() {
            if (this.pk.startsWith('pk_live')) return 'live';
            if (this.pk.startsWith('pk_test')) return 'test';
            return 'none';
        }
     }">

    <p class="text-sm text-gray-500 mb-6">
        Configura las llaves de Stripe que usa el checkout para cobrar. Puedes obtenerlas en
        <a href="https://dashboard.stripe.com/apikeys" target="_blank" rel="noopener" class="text-blue-600 hover:underline">dashboard.stripe.com/apikeys</a>.
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

            {{-- Indicador de modo --}}
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Modo detectado:</span>
                <span x-show="mode === 'live'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Producción (live)
                </span>
                <span x-show="mode === 'test'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pruebas (test)
                </span>
                <span x-show="mode === 'none'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                    Sin configurar
                </span>
            </div>

            {{-- Clave publicable --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave publicable (Publishable key)</label>
                <input type="text" name="stripe_key" x-model="pk"
                       placeholder="pk_live_... o pk_test_..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                <p class="text-xs text-gray-400 mt-1">Comienza con <code class="bg-gray-100 px-1 rounded">pk_</code>. Es visible en el navegador; no es secreta.</p>
            </div>

            {{-- Clave secreta --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave secreta (Secret key)</label>
                <div class="relative">
                    <input :type="showSecret ? 'text' : 'password'" name="stripe_secret" value="{{ $stripeSecret }}"
                           placeholder="sk_live_... o sk_test_..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono pr-11">
                    <button type="button" @click="showSecret = !showSecret"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg x-show="!showSecret" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg x-show="showSecret" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Comienza con <code class="bg-gray-100 px-1 rounded">sk_</code>. <strong>Confidencial</strong>: nunca la compartas.</p>
            </div>

            {{-- Webhook secret --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Secreto del webhook (Webhook signing secret)</label>
                <div class="relative">
                    <input :type="showWebhook ? 'text' : 'password'" name="stripe_webhook_secret" value="{{ $stripeWebhookSecret }}"
                           placeholder="whsec_..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono pr-11">
                    <button type="button" @click="showWebhook = !showWebhook"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg x-show="!showWebhook" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <svg x-show="showWebhook" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    Para confirmar pagos automáticamente. Crea el endpoint en Stripe apuntando a
                    <code class="bg-gray-100 px-1 rounded break-all">{{ url('/stripe/webhook') }}</code> y copia aquí su secreto (<code class="bg-gray-100 px-1 rounded">whsec_</code>).
                </p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Guardar llaves
                </button>
            </div>
        </div>
    </form>

    <p class="text-xs text-gray-400 mt-4">
        Consejo: usa llaves de <strong>test</strong> (pk_test / sk_test) mientras pruebas, y cámbialas por las de <strong>live</strong> al salir a producción.
    </p>
</div>
@endsection
