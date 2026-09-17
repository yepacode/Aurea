@extends('layouts.admin')

@section('title', 'Medios de pago manual')
@section('page_title', 'Medios de pago manual')

@section('content')
<div class="max-w-3xl mx-auto">

    <p class="text-sm text-gray-500 mb-6">Configura los datos que recibirán los clientes al elegir <strong>Transferencia bancaria</strong> en el checkout. Se incluyen en el correo de confirmación de pedido y en la vista de seguimiento. Puedes activar también <strong>Nequi</strong>, <strong>Daviplata</strong> y <strong>Bancolombia a la mano</strong> como medios alternativos.</p>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bank-transfer.settings') }}">
        @method('PUT')
        @csrf

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ==================== Transferencia bancaria ==================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Cuenta bancaria (Bancolombia, Davivienda, BBVA…)</h2>
                    <p class="text-xs text-gray-500">Estos datos activan el método <em>Transferencia</em> en el checkout.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banco *</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $bankName) }}"
                           placeholder="Ej: Bancolombia, Davivienda, BBVA..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titular de la cuenta *</label>
                    <input type="text" name="account_holder" value="{{ old('account_holder', $accountHolder) }}"
                           placeholder="Nombre completo del titular"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de cuenta</label>
                    <select name="account_type"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">— Selecciona —</option>
                        <option value="Ahorros"  @selected(old('account_type', $accountType) === 'Ahorros')>Ahorros</option>
                        <option value="Corriente" @selected(old('account_type', $accountType) === 'Corriente')>Corriente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de cuenta</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $accountNumber) }}"
                           placeholder="Número de cuenta"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono tracking-wider">
                    <p class="text-xs text-gray-400 mt-1">Sin este número el método "Transferencia" no aparece en el checkout.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de documento</label>
                    <select name="document_type"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">— Selecciona —</option>
                        <option value="CC"  @selected(old('document_type', $documentType) === 'CC')>Cédula de ciudadanía (CC)</option>
                        <option value="NIT" @selected(old('document_type', $documentType) === 'NIT')>NIT</option>
                        <option value="CE"  @selected(old('document_type', $documentType) === 'CE')>Cédula de extranjería (CE)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nº de documento</label>
                    <input type="text" name="document_number" value="{{ old('document_number', $documentNumber) }}"
                           placeholder="Número de documento del titular"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIT (si el titular es empresa)</label>
                <input type="text" name="nit" value="{{ old('nit', $nit) }}"
                       placeholder="Ej: 900.123.456-7"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <p class="text-xs text-gray-400 mt-1">Opcional. Se muestra en el correo de transferencia junto con el titular.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones de referencia</label>
                <input type="text" name="reference_instructions" value="{{ old('reference_instructions', $referenceInstructions) }}"
                       placeholder="Ej: Usa tu número de pedido como referencia"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <p class="text-xs text-gray-400 mt-1">Se muestra al cliente en el correo junto con su número de pedido.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas adicionales</label>
                <textarea name="additional_notes" rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                          placeholder="Ej: Realizar la transferencia dentro de las 24 horas...">{{ old('additional_notes', $additionalNotes) }}</textarea>
            </div>
        </div>

        {{-- ==================== Nequi ==================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4 mb-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:#FBEAF4;color:#DA0081;">
                        <span class="text-lg font-bold">N</span>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Nequi</h2>
                        <p class="text-xs text-gray-500">Pagos vía celular. Se muestra en el correo y en el seguimiento.</p>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="nequi_enabled" value="1"
                           @checked(old('nequi_enabled', $nequiEnabled))
                           class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                    <span class="font-medium text-gray-700">Activar</span>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número Nequi</label>
                    <input type="text" name="nequi_number" value="{{ old('nequi_number', $nequiNumber) }}"
                           placeholder="Ej: 300 000 0000"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm font-mono">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones (opcional)</label>
                <textarea name="nequi_instructions" rows="2"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                          placeholder="Ej: Envía a este número y adjunta el comprobante en el seguimiento.">{{ old('nequi_instructions', $nequiInstructions) }}</textarea>
            </div>
        </div>

        {{-- ==================== Daviplata ==================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4 mb-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:#FDECEC;color:#E1002B;">
                        <span class="text-lg font-bold">D</span>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Daviplata</h2>
                        <p class="text-xs text-gray-500">Billetera digital de Davivienda.</p>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="daviplata_enabled" value="1"
                           @checked(old('daviplata_enabled', $daviplataEnabled))
                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="font-medium text-gray-700">Activar</span>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número Daviplata</label>
                    <input type="text" name="daviplata_number" value="{{ old('daviplata_number', $daviplataNumber) }}"
                           placeholder="Ej: 300 000 0000"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-mono">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones (opcional)</label>
                <textarea name="daviplata_instructions" rows="2"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                          placeholder="Ej: Recibirás la confirmación por SMS.">{{ old('daviplata_instructions', $daviplataInstructions) }}</textarea>
            </div>
        </div>

        {{-- ==================== Bancolombia a la mano ==================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4 mb-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:#FFF6E5;color:#FDDA24;">
                        <span class="text-lg font-bold" style="color:#003DA5;">B</span>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Bancolombia a la mano</h2>
                        <p class="text-xs text-gray-500">Billetera digital de Bancolombia. Opcional.</p>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="bancolombia_amano_enabled" value="1"
                           @checked(old('bancolombia_amano_enabled', $bancolombiaAmanoEnabled))
                           class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                    <span class="font-medium text-gray-700">Activar</span>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número Bancolombia a la mano</label>
                    <input type="text" name="bancolombia_amano_number" value="{{ old('bancolombia_amano_number', $bancolombiaAmanoNumber) }}"
                           placeholder="Ej: 300 000 0000"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm font-mono">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg
                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                Guardar datos
            </button>
        </div>
    </form>
</div>
@endsection
