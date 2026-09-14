@extends('layouts.admin')

@php $isNew = ! $zone->exists; @endphp

@section('title', $isNew ? 'Nueva zona de envío' : 'Editar zona: ' . $zone->name)
@section('page_title', $isNew ? 'Nueva zona de envío' : 'Editar zona')

@section('content')
<div class="max-w-4xl">

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ $isNew ? route('admin.shipping-zones.store') : route('admin.shipping-zones.update', $zone) }}"
          class="space-y-6">
        @csrf
        @if(! $isNew) @method('PUT') @endif

        {{-- ── Datos de la zona ── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos de la zona</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la zona *</label>
                    <input type="text" name="name" maxlength="120" required
                           value="{{ old('name', $zone->name) }}"
                           placeholder="Ej: Bogotá y alrededores"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Nombre visible para ti en el admin y para el cliente en la confirmación del pedido.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transportadora *</label>
                    <select name="carrier" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($carriers as $slug => $label)
                            <option value="{{ $slug }}" {{ old('carrier', $zone->carrier) === $slug ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Orden de prioridad</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $zone->sort_order ?? 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Menor número = mayor prioridad si un departamento aparece en varias zonas.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa base ($ COP) *</label>
                    <input type="number" name="base_cost" min="0" required
                           value="{{ old('base_cost', (int) ($zone->base_cost ?? 0)) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Costo del envío para un paquete de hasta 1 kg.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa por kg extra ($ COP)</label>
                    <input type="number" name="extra_kg_cost" min="0"
                           value="{{ old('extra_kg_cost', (int) ($zone->extra_kg_cost ?? 0)) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Opcional. Se suma por cada kg completo por encima de 1 kg.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Días mínimos de entrega *</label>
                    <input type="number" name="delivery_days_min" min="0" max="60" required
                           value="{{ old('delivery_days_min', (int) ($zone->delivery_days_min ?? 1)) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Días máximos de entrega *</label>
                    <input type="number" name="delivery_days_max" min="0" max="60" required
                           value="{{ old('delivery_days_max', (int) ($zone->delivery_days_max ?? 3)) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Debe ser mayor o igual al mínimo.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $zone->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Zona activa (se ofrece a los clientes en el checkout)</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Departamentos cubiertos ── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="deptsSelector()">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Departamentos cubiertos</h2>
                    <p class="text-sm text-gray-500 mt-1">Marca los departamentos donde aplica esta zona. Puedes tener el mismo departamento en varias zonas — gana la que tenga menor <em>Orden de prioridad</em>.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectAll()" class="text-xs text-blue-600 hover:underline">Seleccionar todos</button>
                    <span class="text-gray-300">·</span>
                    <button type="button" @click="clearAll()" class="text-xs text-blue-600 hover:underline">Ninguno</button>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($departments as $dept)
                    @php $isChecked = in_array($dept, old('departments', $assignedDepartments), true); @endphp
                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="departments[]" value="{{ $dept }}"
                               {{ $isChecked ? 'checked' : '' }}
                               class="dept-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $dept }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.shipping-zones.index') }}" class="text-sm text-gray-600 hover:text-gray-800">← Volver</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ $isNew ? 'Crear zona' : 'Guardar cambios' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function deptsSelector() {
        return {
            selectAll() {
                document.querySelectorAll('.dept-checkbox').forEach(cb => cb.checked = true);
            },
            clearAll() {
                document.querySelectorAll('.dept-checkbox').forEach(cb => cb.checked = false);
            },
        };
    }
</script>
@endpush
@endsection
