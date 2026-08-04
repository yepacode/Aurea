@extends('layouts.admin')

@section('title', 'Nuevo código de descuento')
@section('page_title', 'Nuevo código de descuento')

@section('content')
    <form method="POST" action="{{ route('admin.discount-codes.store') }}" class="max-w-3xl space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del código</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código *</label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" required
                               placeholder="Ej: AUREA20" style="text-transform: uppercase"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Se convertirá a mayúsculas automáticamente.</p>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción (interna)</label>
                        <input type="text" id="description" name="description" value="{{ old('description') }}"
                               placeholder="Ej: Campaña de lanzamiento"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de descuento *</label>
                        <select id="type" name="type" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Porcentaje (%)</option>
                            <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Monto fijo ($)</option>
                        </select>
                    </div>
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                        <input type="number" id="value" name="value" value="{{ old('value') }}" required
                               step="0.01" min="0.01" placeholder="20"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Ej: 20 = 20% o $20 según el tipo.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Restricciones</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-1">Monto mínimo de compra</label>
                        <input type="number" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount') }}"
                               step="0.01" min="0" placeholder="Sin mínimo"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-1">Máximo de usos</label>
                        <input type="number" id="max_uses" name="max_uses" value="{{ old('max_uses') }}"
                               min="1" placeholder="Ilimitado"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Dejar vacío para usos ilimitados.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Vigencia</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio</label>
                        <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Dejar vacío para activar de inmediato.</p>
                    </div>
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Fecha de expiración</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Dejar vacío para que no expire.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                    <span class="text-sm text-gray-700">Código activo</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.discount-codes.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Crear código
            </button>
        </div>
    </form>
@endsection
