@extends('layouts.admin')

@section('title', 'Orden #' . $order->id)
@section('page_title', 'Orden #' . $order->id)

@section('content')
    <div class="max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver al listado</a>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                      onsubmit="return confirm('¿Eliminar la orden #{{ $order->id }}? Esta acción no se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-500 hover:text-red-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Order info (left 2 cols) --}}
            <div class="md:col-span-2 space-y-6">

                {{-- Products --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Productos</h2>
                    <table class="w-full">
                        <thead class="text-left text-xs font-medium text-gray-500 uppercase">
                            <tr>
                                <th class="pb-3">Producto</th>
                                <th class="pb-3">Variante</th>
                                <th class="pb-3 text-center">Cant.</th>
                                <th class="pb-3 text-right">Precio</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3 font-medium">{{ $item->product->name }}</td>
                                    <td class="py-3 text-gray-500">{{ $item->variant?->value ?? '—' }}</td>
                                    <td class="py-3 text-center">{{ $item->qty }}</td>
                                    <td class="py-3 text-right">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-medium">${{ number_format($item->total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="text-sm border-t border-gray-200">
                            <tr>
                                <td colspan="4" class="pt-3 text-right text-gray-500">Subtotal</td>
                                <td class="pt-3 text-right font-medium">${{ number_format($order->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if($order->discount_2x1 > 0)
                            <tr>
                                <td colspan="4" class="py-1 text-right text-green-600">Descuento 2×1</td>
                                <td class="py-1 text-right text-green-600">-${{ number_format($order->discount_2x1, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($order->discount_coupon > 0)
                            <tr>
                                <td colspan="4" class="py-1 text-right text-green-600">Cupón{{ $order->discount_code ? " ({$order->discount_code})" : '' }}</td>
                                <td class="py-1 text-right text-green-600">-${{ number_format($order->discount_coupon, 0, ',', '.') }}</td>
                            </tr>
                            @elseif($order->discount_amount > 0 && $order->discount_2x1 == 0)
                            <tr>
                                <td colspan="4" class="py-1 text-right text-green-600">Descuento {{ $order->discount_code ? "({$order->discount_code})" : '' }}</td>
                                <td class="py-1 text-right text-green-600">-${{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="py-1 text-right text-gray-500">Envío</td>
                                <td class="py-1 text-right">{{ $order->shipping > 0 ? '$' . number_format($order->shipping, 0, ',', '.') : 'Gratis' }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="pt-1 text-right font-semibold text-gray-900">Total</td>
                                <td class="pt-1 text-right font-bold text-lg">${{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    @if($order->notes)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Notas</h3>
                            <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Shipping tracking --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ carrier: '{{ $order->shipping_carrier ?? '' }}' }">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <svg class="w-5 h-5 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                        </svg>
                        Datos de envío / Guía
                    </h2>

                    @if($order->tracking_number)
                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-blue-800 uppercase tracking-wide">Paquetería</p>
                                    <p class="text-sm font-medium text-gray-800">{{ $order->shipping_carrier ?? '—' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-semibold text-blue-800 uppercase tracking-wide">No. de guía</p>
                                    <p class="text-sm font-bold text-blue-900 font-mono tracking-wider">{{ $order->tracking_number }}</p>
                                </div>
                            </div>
                            @if($order->tracking_url)
                                <a href="{{ $order->tracking_url }}" target="_blank" class="mt-3 inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                    Rastrear en sitio de paquetería
                                </a>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.orders.tracking', $order) }}">
                        @csrf @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            {{-- Carrier select --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Paquetería</label>
                                <select name="shipping_carrier" x-model="carrier"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="Servientrega" {{ $order->shipping_carrier === 'Servientrega' ? 'selected' : '' }}>Servientrega</option>
                                    <option value="Interrapidísimo" {{ $order->shipping_carrier === 'Interrapidísimo' ? 'selected' : '' }}>Interrapidísimo</option>
                                    <option value="Coordinadora" {{ $order->shipping_carrier === 'Coordinadora' ? 'selected' : '' }}>Coordinadora</option>
                                    <option value="Envía" {{ $order->shipping_carrier === 'Envía' ? 'selected' : '' }}>Envía</option>
                                    <option value="TCC" {{ $order->shipping_carrier === 'TCC' ? 'selected' : '' }}>TCC</option>
                                    <option value="Deprisa" {{ $order->shipping_carrier === 'Deprisa' ? 'selected' : '' }}>Deprisa</option>
                                    <option value="4-72" {{ $order->shipping_carrier === '4-72' ? 'selected' : '' }}>4-72</option>
                                    <option value="FedEx" {{ $order->shipping_carrier === 'FedEx' ? 'selected' : '' }}>FedEx</option>
                                    <option value="DHL Express" {{ $order->shipping_carrier === 'DHL Express' ? 'selected' : '' }}>DHL Express</option>
                                    <option value="Entrega personal" {{ $order->shipping_carrier === 'Entrega personal' ? 'selected' : '' }}>Entrega personal</option>
                                    <option value="Otro" {{ $order->shipping_carrier === 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>

                            {{-- Tracking number --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de guía</label>
                                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}"
                                       placeholder="Ej: 7940 0110 0311"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                        </div>

                        {{-- Tracking URL --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL de rastreo <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <input type="url" name="tracking_url" value="{{ $order->tracking_url }}"
                                   placeholder="https://www.fedex.com/fedextrack/?trknbr=..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{-- Helper links --}}
                            <div class="mt-2 flex flex-wrap gap-2 text-xs" x-show="carrier" x-cloak>
                                <span class="text-gray-400">Rastreo rápido:</span>
                                <template x-if="carrier === 'Servientrega'">
                                    <a href="https://www.servientrega.com/wps/portal/rastreo-envio" target="_blank" class="text-blue-500 hover:underline">servientrega.com rastreo</a>
                                </template>
                                <template x-if="carrier === 'Interrapidísimo'">
                                    <a href="https://interrapidisimo.com/sigue-tu-envio/" target="_blank" class="text-blue-500 hover:underline">interrapidisimo.com rastreo</a>
                                </template>
                                <template x-if="carrier === 'Coordinadora'">
                                    <a href="https://coordinadora.com/rastreo/rastreo-de-guia/" target="_blank" class="text-blue-500 hover:underline">coordinadora.com rastreo</a>
                                </template>
                                <template x-if="carrier === 'Envía'">
                                    <a href="https://envia.co/" target="_blank" class="text-blue-500 hover:underline">envia.co rastreo</a>
                                </template>
                                <template x-if="carrier === 'TCC'">
                                    <a href="https://www.tcc.com.co/rastreo-de-mercancia/" target="_blank" class="text-blue-500 hover:underline">tcc.com.co rastreo</a>
                                </template>
                                <template x-if="carrier === 'Deprisa'">
                                    <a href="https://www.deprisa.com/Seguimiento" target="_blank" class="text-blue-500 hover:underline">deprisa.com rastreo</a>
                                </template>
                                <template x-if="carrier === '4-72'">
                                    <a href="https://www.4-72.com.co/" target="_blank" class="text-blue-500 hover:underline">4-72.com.co rastreo</a>
                                </template>
                                <template x-if="carrier === 'FedEx'">
                                    <a href="https://www.fedex.com/fedextrack/" target="_blank" class="text-blue-500 hover:underline">fedex.com/fedextrack</a>
                                </template>
                                <template x-if="carrier === 'DHL Express'">
                                    <a href="https://www.dhl.com/co-es/home/rastreo.html" target="_blank" class="text-blue-500 hover:underline">dhl.com rastreo</a>
                                </template>
                            </div>
                        </div>

                        {{-- Notify customer --}}
                        <div class="flex items-center gap-2 mb-4 p-3 bg-gray-50 rounded-lg">
                            <input type="hidden" name="notify_customer" value="0">
                            <input type="checkbox" name="notify_customer" value="1" id="notify_customer"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                            <label for="notify_customer" class="text-sm text-gray-700">
                                Notificar al cliente por correo electrónico
                            </label>
                            <span class="text-xs text-gray-400">({{ $order->customer->email }})</span>
                        </div>

                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                            </svg>
                            Guardar datos de envío
                        </button>
                    </form>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Estado del pedido</h2>

                    {{-- Visual status --}}
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'confirmed' => 'bg-blue-100 text-blue-800',
                            'shipped' => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'pending' => 'Pendiente',
                            'confirmed' => 'Confirmada',
                            'shipped' => 'Enviada',
                            'delivered' => 'Entregada',
                            'cancelled' => 'Cancelada',
                        ];
                    @endphp
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf @method('PATCH')
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 mb-3">
                            @foreach($statusLabels as $val => $label)
                                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-2 mb-3 p-2.5 bg-gray-50 rounded-lg">
                            <input type="hidden" name="notify_status" value="0">
                            <input type="checkbox" name="notify_status" value="1" id="notify_status"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                            <label for="notify_status" class="text-xs text-gray-600">
                                Notificar al cliente
                            </label>
                        </div>
                        <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                            Actualizar estado
                        </button>
                    </form>

                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Método de pago</span>
                            <span class="font-medium text-gray-700">
                                @switch($order->payment_method)
                                    @case('card') Tarjeta @break
                                    @case('transfer') Transferencia @break
                                    @case('cash_on_delivery') Contra entrega @break
                                    @default {{ $order->payment_method ?? '—' }}
                                @endswitch
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estado del pago</span>
                            <span class="font-medium {{ $order->payment_status === 'paid' ? 'text-green-600' : ($order->payment_status === 'failed' ? 'text-red-600' : 'text-yellow-600') }}">
                                @switch($order->payment_status)
                                    @case('paid') Pagado @break
                                    @case('pending') Pendiente @break
                                    @case('processing') Procesando @break
                                    @case('failed') Fallido @break
                                    @case('refunded') Reembolsado @break
                                    @default {{ $order->payment_status }}
                                @endswitch
                            </span>
                        </div>

                        {{-- Cambiar manualmente el estado del pago (sirve cuando el cliente pagó por fuera, p. ej. transferencia confirmada por WhatsApp) --}}
                        <form method="POST" action="{{ route('admin.orders.payment-status', $order) }}" class="pt-2 border-t border-gray-100">
                            @csrf @method('PATCH')
                            <label class="block text-[10px] uppercase tracking-wide text-gray-400 mb-1">Cambiar estado del pago</label>
                            <div class="flex gap-2">
                                <select name="payment_status" class="flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach([
                                        'pending' => 'Pendiente',
                                        'processing' => 'Procesando',
                                        'paid' => 'Pagado',
                                        'failed' => 'Fallido',
                                        'refunded' => 'Reembolsado',
                                    ] as $val => $label)
                                        <option value="{{ $val }}" {{ $order->payment_status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                    Guardar
                                </button>
                            </div>
                        </form>

                        @if($order->stripe_payment_intent_id)
                        <div class="flex justify-between">
                            <span>Stripe ID</span>
                            <span class="font-mono text-gray-600 text-[10px]">{{ Str::limit($order->stripe_payment_intent_id, 20) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Bank Transfer Receipt --}}
                @if($order->payment_method === 'transfer')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Comprobante de transferencia</h2>

                    @if($order->payment_receipt)
                        @php $ext = strtolower(pathinfo($order->payment_receipt, PATHINFO_EXTENSION)); @endphp
                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                            <a href="{{ asset('storage/' . $order->payment_receipt) }}" target="_blank">
                                <img src="{{ asset('storage/' . $order->payment_receipt) }}" alt="Comprobante"
                                     class="rounded-lg border border-gray-200 max-h-64 w-full object-contain bg-gray-50 mb-4">
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $order->payment_receipt) }}" target="_blank"
                               class="inline-flex items-center gap-2 text-sm text-blue-600 font-medium hover:underline mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                Ver comprobante (PDF)
                            </a>
                        @endif
                    @else
                        <p class="text-sm text-gray-400 mb-4">El cliente aún no ha subido su comprobante.</p>
                    @endif

                    @if($order->payment_status !== 'paid')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.orders.verify-payment', $order) }}" class="flex-1"
                                  onsubmit="return confirm('¿Marcar este pedido como pagado?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                    Aprobar
                                </button>
                            </form>
                            @if($order->payment_receipt)
                            <form method="POST" action="{{ route('admin.orders.reject-payment', $order) }}" class="flex-1"
                                  onsubmit="return confirm('¿Rechazar este comprobante? El cliente podrá subir uno nuevo.')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                    Rechazar
                                </button>
                            </form>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-sm text-green-600 font-medium bg-green-50 rounded-lg px-4 py-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            Pago verificado
                        </div>
                    @endif
                </div>
                @endif

                {{-- Customer --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Cliente</h2>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-gray-500">Nombre</dt>
                            <dd class="font-medium">{{ $order->customer->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Email</dt>
                            <dd><a href="mailto:{{ $order->customer->email }}" class="text-blue-600 hover:underline">{{ $order->customer->email }}</a></dd>
                        </div>
                        @if($order->customer->phone)
                            <div>
                                <dt class="text-gray-500">Teléfono</dt>
                                <dd><a href="tel:{{ $order->customer->phone }}" class="text-blue-600 hover:underline">{{ $order->customer->phone }}</a></dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Shipping address --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Dirección de envío</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $order->shipping_address }}</p>
                </div>

                {{-- Tracking link for customer --}}
                @if($order->tracking_token)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-sm font-semibold text-gray-800 mb-2">Link de seguimiento</h2>
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ route('order.track', $order->tracking_token) }}" readonly
                               class="flex-1 text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1.5 text-gray-600 font-mono" id="tracking-link">
                        <button onclick="navigator.clipboard.writeText(document.getElementById('tracking-link').value); this.textContent='Copiado!'; setTimeout(() => this.textContent='Copiar', 2000)"
                                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded font-medium transition-colors">
                            Copiar
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
