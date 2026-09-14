@extends('layouts.admin')
@section('title', $bundle->exists ? 'Editar kit' : 'Nuevo kit')
@section('page_title', $bundle->exists ? 'Editar kit' : 'Nuevo kit')

@section('content')
<form action="{{ $bundle->exists ? route('admin.bundles.update', $bundle) : route('admin.bundles.store') }}"
      method="POST" enctype="multipart/form-data"
      x-data="bundleForm({
          products: @js($products->map(fn($p) => ['id'=>$p->id,'name'=>$p->name,'price'=>(int)$p->price])->all()),
          initial: @js($selected),
          bundlePrice: {{ (int) old('price', $bundle->price ?? 0) }},
          bundleCompare: {{ (int) old('compare_price', $bundle->compare_price ?? 0) }},
      })">
    @csrf
    @if($bundle->exists) @method('PUT') @endif

    <div class="max-w-4xl space-y-6">
        {{-- Info básica --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Datos del kit</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del kit *</label>
                    <input type="text" name="name" value="{{ old('name', $bundle->name ?? '') }}" required maxlength="255"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="ej. Ritual completo cutícula">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $bundle->sort_order ?? 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="3" maxlength="2000"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                          placeholder="Un pequeño relato del kit: para quién es, cómo se usa, qué logra…">{{ old('description', $bundle->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen collage (opcional)</label>
                <p class="text-xs text-gray-500 mb-2">Composición floral con 3-4 productos · máx. 5 MB. Si no la subes, generamos un collage automático con las fotos de los productos.</p>
                @if($bundle->image)
                    <div class="mb-2 flex items-center gap-3">
                        <img src="{{ $bundle->image_url }}" alt="" class="h-20 rounded-lg border border-gray-200 object-cover">
                        <label class="flex items-center gap-1 text-xs text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_image" value="1"> Quitar imagen actual
                        </label>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
            </div>
        </div>

        {{-- Precios --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Precios (COP)</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio del kit *</label>
                    <input type="number" name="price" min="0" step="1" required
                           x-model.number="bundlePrice"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <p class="text-xs text-gray-500 mt-1">Lo que paga el cliente.</p>
                    @error('price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio comparativo (suma normal) *</label>
                    <input type="number" name="compare_price" min="0" step="1" required
                           x-model.number="bundleCompare"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Sugerido: <button type="button" @click="bundleCompare = itemsSum()"
                                          class="underline" style="color:#BE9A53;">$<span x-text="itemsSum().toLocaleString('es-CO')"></span></button>
                        (suma de los productos elegidos).
                    </p>
                    @error('compare_price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="rounded-lg p-4" style="background:#FBF4E6;border:1px solid rgba(217,181,109,.30);">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color:#BE9A53;font-weight:600;">Ahorro calculado</p>
                        <p class="text-lg" style="font-family:'Playfair Display',serif;color:#2E2A26;">
                            <span x-text="'$' + savings().toLocaleString('es-CO')"></span>
                            <span class="text-sm" style="color:#8E7F6F;">(<span x-text="savingsPercent()"></span>%)</span>
                        </p>
                    </div>
                    <p class="text-xs" style="color:#8E7F6F;" x-show="savings() < 0" x-cloak>
                        ⚠ El precio del kit es MAYOR al comparativo — no habrá ahorro.
                    </p>
                </div>
            </div>
        </div>

        {{-- Productos del kit --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Productos incluidos</h2>
                <button type="button" @click="addRow()"
                        class="text-sm px-3 py-1.5 rounded-lg text-white"
                        style="background:#D9B56D;" onmouseover="this.style.background='#BE9A53'" onmouseout="this.style.background='#D9B56D'">
                    + Agregar producto
                </button>
            </div>

            <template x-if="rows.length === 0">
                <p class="text-sm text-gray-400 py-6 text-center">Sin productos aún. Agrega al menos uno.</p>
            </template>

            <div class="space-y-2">
                <template x-for="(row, idx) in rows" :key="row.uid">
                    <div class="flex items-center gap-2 p-3 rounded-lg" style="background:#FBF8F2;border:1px solid #f0e6d3;">
                        <span class="text-xs text-gray-400 w-6 text-center" x-text="idx + 1"></span>

                        <select name="items[]" x-model.number="row.product_id" required
                                class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">
                            <option value="">— Elige un producto —</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="p.id" x-text="p.name + ' — $' + p.price.toLocaleString('es-CO')"
                                        :selected="p.id === row.product_id"></option>
                            </template>
                        </select>

                        <label class="text-xs text-gray-500">Cant.</label>
                        <input type="number" name="quantities[]" x-model.number="row.quantity" min="1" step="1" value="1"
                               class="w-20 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white">

                        <input type="hidden" name="sort_orders[]" :value="idx">

                        <button type="button" @click="removeRow(idx)"
                                class="text-red-500 hover:text-red-700 text-sm px-2 py-1"
                                aria-label="Quitar">✕</button>
                    </div>
                </template>
            </div>

            <p class="text-xs text-gray-500 mt-2">
                Suma de los productos elegidos:
                <strong style="color:#BE9A53;">$<span x-text="itemsSum().toLocaleString('es-CO')"></span></strong>
            </p>
        </div>

        {{-- Visibilidad --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Visibilidad y vigencia</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-start gap-2 cursor-pointer p-3 rounded-lg border border-gray-200">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $bundle->is_active ?? true) ? 'checked' : '' }}
                           class="mt-0.5 w-4 h-4" style="accent-color:#D9B56D;">
                    <div>
                        <span class="text-sm font-medium">Kit activo</span>
                        <p class="text-xs text-gray-500">Visible en /kits y en el home</p>
                    </div>
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Publicar desde</label>
                    <input type="datetime-local" name="starts_at"
                           value="{{ old('starts_at', optional($bundle->starts_at ?? null)->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Termina el</label>
                    <input type="datetime-local" name="ends_at"
                           value="{{ old('ends_at', optional($bundle->ends_at ?? null)->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-lg font-semibold text-sm text-white" style="background:#D9B56D;">
                {{ $bundle->exists ? 'Guardar cambios' : 'Crear kit' }}
            </button>
            <a href="{{ route('admin.bundles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function bundleForm({ products, initial, bundlePrice, bundleCompare }) {
        let uid = 0;
        const initRows = (initial || []).map(r => ({
            uid: ++uid,
            product_id: Number(r.product_id) || null,
            quantity:   Number(r.quantity)   || 1,
        }));
        return {
            products,
            rows: initRows,
            bundlePrice,
            bundleCompare,
            addRow() { this.rows.push({ uid: ++uid, product_id: null, quantity: 1 }); },
            removeRow(i) { this.rows.splice(i, 1); },
            itemsSum() {
                return this.rows.reduce((acc, row) => {
                    const p = this.products.find(x => x.id === Number(row.product_id));
                    if (!p) return acc;
                    return acc + (p.price * (Number(row.quantity) || 1));
                }, 0);
            },
            savings() { return Math.max(0, (this.bundleCompare || 0) - (this.bundlePrice || 0)); },
            savingsPercent() {
                if (!this.bundleCompare || this.bundleCompare <= 0) return 0;
                return Math.round(((this.bundleCompare - this.bundlePrice) / this.bundleCompare) * 100);
            },
        };
    }
</script>
@endpush

@endsection
