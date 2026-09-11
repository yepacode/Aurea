@extends('layouts.admin')

@section('title', 'Transferencia bancaria')
@section('page_title', 'Transferencia bancaria')

@section('content')
<div class="max-w-2xl mx-auto">

    <p class="text-sm text-gray-500 mb-6">Configura los datos bancarios que recibirán los clientes al pagar por transferencia. Estos datos se incluyen en el correo de confirmación de pedido.</p>

    <form method="POST" action="{{ route('admin.bank-transfer.settings') }}">
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

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Guardar datos bancarios
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
