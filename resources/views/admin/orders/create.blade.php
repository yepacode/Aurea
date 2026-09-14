@extends('layouts.admin')

@section('title', 'Crear pedido manual')
@section('page_title', 'Crear pedido manual')

@section('content')
<div class="max-w-5xl mx-auto"
     x-data="manualOrderForm({{ $products->toJson() }})">

    <p class="text-sm text-gray-500 mb-6">
        Registra manualmente un pedido cerrado por WhatsApp, teléfono o venta directa.
        El pedido queda en el historial del cliente y en tus reportes de ventas.
    </p>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-5">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6">
        @csrf

        {{-- Cliente --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Cliente</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Si ya existe un cliente con este email, el pedido se le asocia.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>

        {{-- Envío --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Envío</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
                    <input type="text" name="shipping_address" value="{{ old('shipping_address') }}" required
                           placeholder="Calle, número, apto/interior"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departamento *</label>
                    <input type="text" name="state" value="{{ old('state') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código postal</label>
                    <input type="text" name="zip_code" value="{{ old('zip_code') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Costo de envío</label>
                    <input type="number" step="0.01" min="0" name="shipping" x-model.number="shipping"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>

        {{-- Productos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800">Productos</h2>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Agregar producto
                </button>
            </div>

            <template x-if="items.length === 0">
                <div class="text-center text-sm text-gray-400 py-6 border border-dashed border-gray-300 rounded-lg">
                    Aún no hay productos en el pedido. Da clic en "Agregar producto".
                </div>
            </template>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="item._key">
                    <div class="grid grid-cols-12 gap-2 items-start border border-gray-200 rounded-lg p-3">
                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Producto</label>
                            <select :name="`items[${idx}][product_id]`"
                                    x-model.number="item.product_id"
                                    @change="onProductChange(idx)"
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                <option value="">-- Selecciona --</option>
                                <template x-for="p in products" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-span-4 sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cantidad</label>
                            <input type="number" min="1" max="10000" step="1"
                                   :name="`items[${idx}][qty]`"
                                   x-model.number="item.qty"
                                   required
                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div class="col-span-4 sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Precio unit.</label>
                            <input type="number" min="0" step="0.01"
                                   :name="`items[${idx}][unit_price]`"
                                   x-model.number="item.unit_price"
                                   required
                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div class="col-span-3 sm:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Subtotal</label>
                            <div class="text-sm font-mono py-2 px-2 bg-gray-50 rounded-lg"
                                 x-text="formatMoney((item.qty || 0) * (item.unit_price || 0))"></div>
                        </div>
                        <div class="col-span-1 flex items-end justify-end pt-5">
                            <button type="button" @click="removeItem(idx)"
                                    class="text-red-500 hover:bg-red-50 p-1.5 rounded-lg"
                                    title="Quitar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Pago --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Pago</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de pago *</label>
                    <select name="payment_method" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transferencia bancaria</option>
                        <option value="cash_on_delivery" {{ old('payment_method') === 'cash_on_delivery' ? 'selected' : '' }}>Contra entrega</option>
                        <option value="epayco" {{ old('payment_method') === 'epayco' ? 'selected' : '' }}>ePayco</option>
                        <option value="manual" {{ old('payment_method', 'manual') === 'manual' ? 'selected' : '' }}>Manual (ya recibido por fuera)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">"Manual" es cuando ya recibiste el pago por otro medio (efectivo, Nequi, etc).</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado del pago *</label>
                    <select name="payment_status" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="paid" {{ old('payment_status', 'paid') === 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="processing" {{ old('payment_status') === 'processing' ? 'selected' : '' }}>En proceso</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                    <textarea name="notes" rows="2" maxlength="1000"
                              placeholder="Ej. Confirmado por WhatsApp, entregar en portería, etc."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Total y acciones --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between">
            <div class="text-sm">
                <div class="text-gray-500">Subtotal: <span class="font-mono text-gray-800" x-text="formatMoney(subtotal)"></span></div>
                <div class="text-gray-500">Envío: <span class="font-mono text-gray-800" x-text="formatMoney(shipping)"></span></div>
                <div class="text-lg font-semibold text-gray-900 mt-1">Total: <span class="font-mono" x-text="formatMoney(subtotal + Number(shipping || 0))"></span></div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders.index') }}"
                   class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Crear pedido
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function manualOrderForm(products) {
    return {
        products: products,
        items: [],
        shipping: 0,
        _seq: 0,
        get subtotal() {
            return this.items.reduce((s, i) => s + (Number(i.qty) || 0) * (Number(i.unit_price) || 0), 0);
        },
        addItem() {
            this.items.push({ _key: ++this._seq, product_id: '', qty: 1, unit_price: 0 });
        },
        removeItem(idx) {
            this.items.splice(idx, 1);
        },
        onProductChange(idx) {
            const item = this.items[idx];
            const p = this.products.find(x => x.id === Number(item.product_id));
            if (p) item.unit_price = Number(p.price);
        },
        formatMoney(v) {
            const n = Number(v || 0);
            return '$' + n.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        },
        init() {
            if (this.items.length === 0) this.addItem();
        },
    }
}
</script>
@endsection
