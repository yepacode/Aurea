@extends('layouts.admin')
@section('title', $plan->exists ? 'Editar plan' : 'Nuevo plan')
@section('page_title', $plan->exists ? 'Editar plan de suscripción' : 'Nuevo plan de suscripción')

@section('content')
<form action="{{ $plan->exists ? route('admin.subscription-plans.update', $plan) : route('admin.subscription-plans.store') }}"
      method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-6">
    @csrf
    @if($plan->exists) @method('PUT') @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Datos del plan</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" required maxlength="255"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="description" rows="3" maxlength="2000"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $plan->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del plan (opcional)</label>
            @if($plan->image)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ asset('storage/'.$plan->image) }}" alt="" class="h-20 rounded-lg border border-gray-200 object-cover">
                    <label class="flex items-center gap-1 text-xs text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1"> Quitar imagen actual
                    </label>
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="block w-full text-xs text-gray-500">
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="text-sm text-gray-700">Plan activo (visible en la web)</label>
        </div>
    </div>

    {{-- Precios --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Precios & entrega</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio del plan *</label>
                <input type="number" name="base_price" min="0" step="0.01" required
                       value="{{ old('base_price', $plan->base_price ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-500 mt-1">Lo que paga la suscriptora.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio regular (tachado) *</label>
                <input type="number" name="regular_price" min="0" step="0.01" required
                       value="{{ old('regular_price', $plan->regular_price ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-500 mt-1">Suma de precios sueltos.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">% descuento (opcional)</label>
                <input type="number" name="discount_percent" min="0" max="99"
                       value="{{ old('discount_percent', $plan->discount_percent ?? 0) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-500 mt-1">Si es 0 se calcula automáticamente.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cada N días *</label>
                <input type="number" name="interval_days" min="1" max="365" required
                       value="{{ old('interval_days', $plan->interval_days ?? 30) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje de entrega (opcional)</label>
            <input type="text" name="delivery_days_message" maxlength="120"
                   value="{{ old('delivery_days_message', $plan->delivery_days_message ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                   placeholder="ej. Llega en 3-5 días hábiles">
        </div>
    </div>

    {{-- Productos --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4"
         x-data='{
             products: @json($products->map(fn($p) => ["id"=>$p->id,"name"=>$p->name,"price"=>(float)$p->price])->values()),
             items: @json($selected ?: []),
             addItem() { this.items.push({product_id:null, quantity:1, sort_order:this.items.length}); },
             removeItem(i) { this.items.splice(i,1); },
         }'>
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Productos incluidos</h2>
            <button type="button" @click="addItem"
                    class="text-sm px-3 py-1.5 rounded-lg text-white" style="background:#D9B56D;">+ Agregar producto</button>
        </div>

        <template x-if="items.length === 0">
            <p class="text-sm text-gray-500">Aún no hay productos. Agrega los que vienen en cada entrega.</p>
        </template>

        <template x-for="(it, i) in items" :key="i">
            <div class="grid grid-cols-12 gap-2 items-end">
                <div class="col-span-7">
                    <label class="block text-xs text-gray-500">Producto</label>
                    <select :name="'items[]'" x-model.number="it.product_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">— Selecciona —</option>
                        <template x-for="p in products" :key="p.id">
                            <option :value="p.id" x-text="p.name + ' — $' + Number(p.price).toLocaleString('es-CO')"></option>
                        </template>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500">Cant.</label>
                    <input type="number" :name="'quantities[]'" min="1" x-model.number="it.quantity"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500">Orden</label>
                    <input type="number" :name="'sort_orders[]'" min="0" x-model.number="it.sort_order"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-1">
                    <button type="button" @click="removeItem(i)" class="text-red-600 text-xs">Quitar</button>
                </div>
            </div>
        </template>
    </div>

    <div class="flex gap-3 justify-end">
        <a href="{{ route('admin.subscription-plans.index') }}" class="text-sm text-gray-600 px-4 py-2">Cancelar</a>
        <button type="submit" class="text-sm text-white px-6 py-2 rounded-lg font-medium" style="background:#2E2A26;">
            {{ $plan->exists ? 'Guardar cambios' : 'Crear plan' }}
        </button>
    </div>
</form>
@endsection
