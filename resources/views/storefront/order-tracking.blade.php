@extends('layouts.app')

@section('title', "Pedido #{$order->id} | Belleza Áurea")
@section('robots', 'noindex, nofollow')

@section('content')
<section class="py-12 md:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="font-brand text-3xl md:text-4xl font-bold text-text-dark">
                Pedido <span class="text-primary">#{{ $order->id }}</span>
            </h1>
            <p class="mt-2 text-text-muted">
                Realizado el {{ $order->created_at->format('d/m/Y') }} a las {{ $order->created_at->format('H:i') }}
            </p>
        </div>

        {{-- Status Timeline --}}
        @php
            $statuses = [
                'pending'   => ['label' => 'Pendiente',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'confirmed' => ['label' => 'Confirmado',  'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'shipped'   => ['label' => 'Enviado',     'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12'],
                'delivered' => ['label' => 'Entregado',   'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
            ];

            $statusKeys = array_keys($statuses);
            $currentIndex = $order->status === 'cancelled' ? -1 : array_search($order->status, $statusKeys);
        @endphp

        @if($order->status === 'cancelled')
            <div class="bg-danger/10 border border-danger/20 rounded-xl p-6 text-center mb-8">
                <svg class="w-12 h-12 mx-auto text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-3 text-lg font-semibold text-danger">Pedido cancelado</p>
            </div>
        @else
            <div class="bg-white border border-border-light rounded-xl p-6 md:p-8 mb-8">
                <div class="flex items-center justify-between relative">
                    {{-- Progress line --}}
                    <div class="absolute top-5 left-0 right-0 h-0.5 bg-muted mx-10 md:mx-16"></div>
                    <div class="absolute top-5 left-0 h-0.5 bg-primary mx-10 md:mx-16 transition-all duration-500"
                         style="width: {{ $currentIndex > 0 ? ($currentIndex / (count($statuses) - 1)) * (100 - 15) : 0 }}%"></div>

                    @foreach($statuses as $key => $info)
                        @php
                            $index = array_search($key, $statusKeys);
                            $isActive = $index <= $currentIndex;
                            $isCurrent = $index === $currentIndex;
                        @endphp
                        <div class="flex flex-col items-center relative z-10 flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300
                                {{ $isCurrent ? 'bg-primary text-white ring-4 ring-primary/20 scale-110' : ($isActive ? 'bg-primary text-white' : 'bg-muted text-text-muted') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $info['icon'] }}"/>
                                </svg>
                            </div>
                            <span class="mt-2 text-xs md:text-sm font-medium {{ $isActive ? 'text-primary' : 'text-text-muted' }}">
                                {{ $info['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tracking info (when shipped) --}}
        @if($order->tracking_number)
            <div class="bg-white border border-primary/20 rounded-xl p-6 mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                            </svg>
                        </div>
                        <div>
                            @if($order->shipping_carrier)
                                <p class="text-xs text-text-muted uppercase tracking-wide font-semibold">Paquetería</p>
                                <p class="font-semibold text-text-dark">{{ $order->shipping_carrier }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-text-muted uppercase tracking-wide font-semibold">No. de guía</p>
                        <p class="font-bold text-primary text-lg font-mono tracking-wider">{{ $order->tracking_number }}</p>
                    </div>
                    @if($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank"
                           class="inline-flex items-center gap-2 bg-primary hover:bg-primary-light text-white px-6 py-3 rounded-lg font-semibold text-sm transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                            </svg>
                            Rastrear envío
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Bank Transfer: Receipt Upload --}}
        @if($order->payment_method === 'transfer')
            @if($order->payment_status === 'paid')
                {{-- Payment verified --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-green-800">Pago verificado</p>
                        <p class="text-sm text-green-600">Tu transferencia ha sido confirmada.</p>
                    </div>
                </div>
            @elseif($order->payment_receipt)
                {{-- Receipt uploaded, pending verification --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-amber-800">Comprobante subido</p>
                            <p class="text-sm text-amber-600">Estamos verificando tu pago. Te notificaremos cuando sea confirmado.</p>
                        </div>
                    </div>
                    @php
                        $ext = strtolower(pathinfo($order->payment_receipt, PATHINFO_EXTENSION));
                    @endphp
                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                        <img src="{{ asset('storage/' . $order->payment_receipt) }}" alt="Comprobante de pago"
                             class="rounded-lg border border-amber-200 max-h-48 mt-2">
                    @else
                        <a href="{{ asset('storage/' . $order->payment_receipt) }}" target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-amber-700 font-medium mt-2 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            Ver comprobante (PDF)
                        </a>
                    @endif

                    {{-- Allow re-upload --}}
                    <form method="POST" action="{{ route('order.uploadReceipt', $order->tracking_token) }}" enctype="multipart/form-data" class="mt-4 pt-3 border-t border-amber-200">
                        @csrf
                        <p class="text-xs text-amber-600 mb-2">¿Subiste el archivo equivocado? Puedes reemplazarlo:</p>
                        <div class="flex items-center gap-3">
                            <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                                   class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                            <button type="submit" class="px-4 py-1.5 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
                                Reemplazar
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- No receipt yet - show upload form + bank details --}}
                <div class="bg-white border border-secondary/30 rounded-xl overflow-hidden mb-6">
                    <div class="bg-secondary/5 px-5 py-4 border-b border-secondary/20">
                        <h3 class="font-brand font-semibold text-text-dark flex items-center gap-2">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                            </svg>
                            Realiza tu transferencia
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        {{-- Bank details --}}
                        @if(!empty($bankDetails['clabe']))
                        <div class="bg-blue-50/50 rounded-lg p-4 space-y-2 text-sm">
                            @if($bankDetails['bank_name'])
                            <div class="flex justify-between" style="gap:12px;"><span class="text-text-muted" style="flex-shrink:0;">Banco:</span><span class="font-semibold text-text-dark" style="text-align:right;overflow-wrap:anywhere;min-width:0;">{{ $bankDetails['bank_name'] }}</span></div>
                            @endif
                            @if($bankDetails['account_holder'])
                            <div class="flex justify-between" style="gap:12px;"><span class="text-text-muted" style="flex-shrink:0;">Beneficiario:</span><span class="font-semibold text-text-dark" style="text-align:right;overflow-wrap:anywhere;min-width:0;">{{ $bankDetails['account_holder'] }}</span></div>
                            @endif
                            <div class="flex justify-between" style="gap:12px;"><span class="text-text-muted" style="flex-shrink:0;">CLABE:</span><span class="font-bold text-text-dark font-mono" style="text-align:right;word-break:break-all;min-width:0;">{{ $bankDetails['clabe'] }}</span></div>
                            @if($bankDetails['account_number'])
                            <div class="flex justify-between" style="gap:12px;"><span class="text-text-muted" style="flex-shrink:0;">No. cuenta:</span><span class="font-semibold text-text-dark" style="text-align:right;word-break:break-all;min-width:0;">{{ $bankDetails['account_number'] }}</span></div>
                            @endif
                            <div class="flex justify-between" style="gap:12px;"><span class="text-text-muted" style="flex-shrink:0;">Referencia:</span><span class="font-bold text-secondary" style="text-align:right;overflow-wrap:anywhere;min-width:0;">Pedido #{{ $order->id }}</span></div>
                            <div class="flex justify-between" style="gap:12px;"><span class="text-text-muted" style="flex-shrink:0;">Monto:</span><span class="font-bold text-secondary text-base" style="text-align:right;">${{ number_format($order->total, 0, ',', '.') }}</span></div>
                        </div>
                        @endif

                        {{-- Upload form --}}
                        <form method="POST" action="{{ route('order.uploadReceipt', $order->tracking_token) }}" enctype="multipart/form-data">
                            @csrf
                            <p class="text-sm text-text-muted mb-3">Una vez realizada tu transferencia, sube tu comprobante de pago:</p>
                            <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20 mb-3">
                            @error('receipt')
                                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="w-full bg-secondary hover:bg-secondary/90 text-white py-3 rounded-lg font-semibold transition-colors">
                                Subir comprobante de pago
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-6">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">{{ session('error') }}</div>
            @endif
        @endif

        {{-- Order Details --}}
        <div class="grid md:grid-cols-3 gap-6">

            {{-- Items (2 cols) --}}
            <div class="md:col-span-2 bg-white border border-border-light rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border-light">
                    <h2 class="font-brand text-lg font-bold text-text-dark">Productos</h2>
                </div>
                <div class="divide-y divide-border-light">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 px-6 py-4">
                        @if($item->product && $item->product->images)
                            <img src="{{ asset('storage/' . ($item->product->images[0] ?? '')) }}"
                                 alt="{{ $item->product->name }}"
                                 class="w-16 h-16 object-cover rounded-lg bg-bg-light">
                        @else
                            <div class="w-16 h-16 bg-bg-light rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-text-dark truncate">{{ $item->product->name ?? 'Producto' }}</p>
                            @if($item->variant)
                                <p class="text-sm text-text-muted">{{ $item->variant->name ?? $item->variant->value ?? '' }}</p>
                            @endif
                            <p class="text-sm text-text-muted">Cant: {{ $item->qty }}</p>
                        </div>
                        <p class="font-semibold text-text-dark">${{ number_format($item->total, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
                {{-- Totals --}}
                <div class="px-6 py-4 bg-bg-light space-y-2">
                    <div class="flex justify-between text-sm text-text-muted">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount_2x1 > 0)
                    <div class="flex justify-between text-sm text-success">
                        <span>Descuento 2×1</span>
                        <span>-${{ number_format($order->discount_2x1, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($order->discount_coupon > 0)
                    <div class="flex justify-between text-sm text-success">
                        <span>Cupón{{ $order->discount_code ? " ({$order->discount_code})" : '' }}</span>
                        <span>-${{ number_format($order->discount_coupon, 0, ',', '.') }}</span>
                    </div>
                    @elseif($order->discount_amount > 0 && $order->discount_2x1 == 0)
                    {{-- Backward compat: legacy orders without split --}}
                    <div class="flex justify-between text-sm text-success">
                        <span>Descuento {{ $order->discount_code ? "({$order->discount_code})" : '' }}</span>
                        <span>-${{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm text-text-muted">
                        <span>Envío</span>
                        <span>{{ $order->shipping > 0 ? '$' . number_format($order->shipping, 0, ',', '.') : 'Gratis' }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-primary pt-2 border-t border-border-light">
                        <span>Total</span>
                        <span>${{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Payment --}}
                <div class="bg-white border border-border-light rounded-xl p-6">
                    <h3 class="font-brand font-bold text-text-dark mb-3">Pago</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-text-muted">Método</span>
                            <span class="font-medium text-text-dark">
                                @switch($order->payment_method)
                                    @case('card') Tarjeta @break
                                    @case('transfer') Transferencia @break
                                    @case('cash_on_delivery') Contra entrega @break
                                    @default {{ $order->payment_method }}
                                @endswitch
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-muted">Estado</span>
                            <span class="inline-flex items-center gap-1 font-medium
                                {{ $order->payment_status === 'paid' ? 'text-success' : ($order->payment_status === 'failed' ? 'text-danger' : 'text-warning') }}">
                                <span class="w-2 h-2 rounded-full
                                    {{ $order->payment_status === 'paid' ? 'bg-success' : ($order->payment_status === 'failed' ? 'bg-danger' : 'bg-warning') }}"></span>
                                @switch($order->payment_status)
                                    @case('paid') Pagado @break
                                    @case('pending') Pendiente @break
                                    @case('processing') Procesando @break
                                    @case('failed') Fallido @break
                                    @default {{ $order->payment_status }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Shipping address --}}
                <div class="bg-white border border-border-light rounded-xl p-6">
                    <h3 class="font-brand font-bold text-text-dark mb-3">Dirección de envío</h3>
                    <p class="text-sm text-text-muted leading-relaxed">{{ $order->shipping_address }}</p>
                </div>

                {{-- Customer --}}
                <div class="bg-white border border-border-light rounded-xl p-6">
                    <h3 class="font-brand font-bold text-text-dark mb-3">Datos del cliente</h3>
                    <div class="space-y-1 text-sm" style="min-width:0;">
                        <p class="font-medium text-text-dark" style="overflow-wrap:anywhere;">{{ $order->customer->name }}</p>
                        <p class="text-text-muted" style="overflow-wrap:anywhere;">{{ $order->customer->email }}</p>
                        @if($order->customer->phone)
                            <p class="text-text-muted" style="overflow-wrap:anywhere;">{{ $order->customer->phone }}</p>
                        @endif
                    </div>
                </div>

                {{-- Help --}}
                @php
                    $helpEmail = \Illuminate\Support\Str::startsWith(trim((string) config('legal.email')), '[') ? null : config('legal.email');
                @endphp
                <div class="bg-primary/5 border border-primary/10 rounded-xl p-6 text-center">
                    <p class="text-sm text-text-muted">¿Necesitas ayuda?</p>
                    @if($helpEmail)
                        <a href="mailto:{{ $helpEmail }}" class="text-sm font-semibold text-primary hover:underline" style="overflow-wrap:anywhere;display:inline-block;max-width:100%;">
                            {{ $helpEmail }}
                        </a>
                    @else
                        <a href="{{ route('contact') }}" class="text-sm font-semibold text-primary hover:underline">
                            Escríbenos aquí
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
