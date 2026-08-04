@extends('layouts.admin')

@section('title', 'Configuración de envíos')
@section('page_title', 'Envíos')

@section('content')
    <div class="max-w-4xl space-y-8">

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- General settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                Configuración general
            </h2>

            <form method="POST" action="{{ route('admin.shipping.settings') }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="default_price" class="block text-sm font-medium text-gray-700 mb-1">Tarifa de envío default ($)</label>
                        <input type="number" id="default_price" name="default_price"
                               value="{{ old('default_price', $defaultPrice) }}"
                               step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Se aplica cuando el estado del cliente no tiene tarifa específica.</p>
                    </div>
                    <div>
                        <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 mb-1">Envío gratis a partir de ($)</label>
                        <input type="number" id="free_shipping_threshold" name="free_shipping_threshold"
                               value="{{ old('free_shipping_threshold', $freeThreshold) }}"
                               step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Pon 0 para desactivar envío gratis automático.</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mt-3 bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                        @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                    </div>
                @endif

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>

        {{-- State rates --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                    </svg>
                    Zonas y tarifas de envío
                </h2>
                <p class="text-sm text-gray-500 mt-1">Crea las zonas que quieras (departamento, ciudad, "Nacional", etc.), ponles su precio y actívalas o desactívalas. Las zonas activas tienen prioridad sobre la tarifa default.</p>
            </div>

            {{-- Add new rate --}}
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <form method="POST" action="{{ route('admin.shipping.store') }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Zona *</label>
                        <input type="text" name="state" required maxlength="100"
                               value="{{ old('state') }}"
                               placeholder="Ej: Santander, Bogotá, Nacional..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Precio ($) *</label>
                        <input type="number" name="price" required step="0.01" min="0" placeholder="0.00"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Agregar
                    </button>
                </form>
                <p class="text-xs text-gray-400 mt-2">Consejo: pon precio <strong>0</strong> para envío gratis en esa zona.</p>
            </div>

            {{-- Rates table --}}
            @if($rates->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <p class="text-sm">Aún no has creado zonas de envío.</p>
                    <p class="text-xs mt-1">Se usará la tarifa default para todos los pedidos.</p>
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Zona</th>
                            <th class="px-6 py-3">Precio</th>
                            <th class="px-6 py-3">Activa</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($rates as $rate)
                        <tr class="hover:bg-gray-50 group" x-data="{ editing: false }">
                            {{-- View mode --}}
                            <template x-if="!editing">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">{{ $rate->state ?? '—' }}</span>
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium {{ $rate->price == 0 ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ $rate->price == 0 ? 'Gratis' : '$' . number_format($rate->price, 0, ',', '.') }}
                                    </span>
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-6 py-4">
                                    {{-- Toggle rápido: activa/desactiva sin entrar a editar --}}
                                    <form method="POST" action="{{ route('admin.shipping.update', $rate) }}" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="state" value="{{ $rate->state }}">
                                        <input type="hidden" name="price" value="{{ $rate->price }}">
                                        <input type="hidden" name="is_active" value="{{ $rate->is_active ? '0' : '1' }}">
                                        <label class="relative inline-flex items-center cursor-pointer" title="{{ $rate->is_active ? 'Desactivar zona' : 'Activar zona' }}">
                                            <input type="checkbox" {{ $rate->is_active ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                                        </label>
                                        <span class="text-xs font-medium {{ $rate->is_active ? 'text-green-700' : 'text-gray-500' }}">{{ $rate->is_active ? 'Activa' : 'Inactiva' }}</span>
                                    </form>
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button @click="editing = true" class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.shipping.destroy', $rate) }}"
                                              onsubmit="return confirm('¿Eliminar tarifa de {{ $rate->state }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </template>

                            {{-- Edit mode --}}
                            <template x-if="editing">
                                <td colspan="4" class="px-6 py-3">
                                    <form method="POST" action="{{ route('admin.shipping.update', $rate) }}" class="flex flex-wrap items-end gap-3">
                                        @csrf @method('PUT')
                                        <div class="flex-1 min-w-[180px]">
                                            <label class="block text-xs text-gray-500 mb-1">Zona</label>
                                            <input type="text" name="state" required maxlength="100" value="{{ $rate->state }}"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="w-28">
                                            <label class="block text-xs text-gray-500 mb-1">Precio</label>
                                            <input type="number" name="price" value="{{ $rate->price }}" required step="0.01" min="0"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" name="is_active" value="1" {{ $rate->is_active ? 'checked' : '' }} class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                            Guardar
                                        </button>
                                        <button type="button" @click="editing = false" class="text-gray-500 hover:text-gray-700 px-2 py-1.5 text-sm">
                                            Cancelar
                                        </button>
                                    </form>
                                </td>
                            </template>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
