<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Mail\OrderStatusUpdate;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('admin.orders.create', [
            'products' => \App\Models\Product::active()->orderBy('name')->get(['id', 'name', 'price']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:10',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1|max:10000',
            'items.*.unit_price' => 'required|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:transfer,cash_on_delivery,epayco,manual',
            'payment_status' => 'required|in:pending,paid,processing',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer = \App\Models\Customer::firstOrCreate(
            ['email' => $data['customer_email']],
            ['name' => $data['customer_name'], 'phone' => $data['customer_phone'] ?? null]
        );

        $subtotal = 0;
        foreach ($data['items'] as $i) {
            $subtotal += $i['unit_price'] * $i['qty'];
        }
        $shipping = (float) ($data['shipping'] ?? 0);
        $total = $subtotal + $shipping;

        $addressLine = implode(', ', array_filter([
            $data['shipping_address'],
            $data['city'] ?? null,
            $data['state'],
            $data['zip_code'] ?? null,
        ]));

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => $data['payment_status'] === 'paid' ? 'confirmed' : 'pending',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount_amount' => 0,
            'discount_2x1' => 0,
            'discount_coupon' => 0,
            'total' => $total,
            'payment_method' => $data['payment_method'],
            'payment_status' => $data['payment_status'],
            'shipping_address' => $addressLine,
            'notes' => 'MANUAL: ' . ($data['notes'] ?? 'creado por admin'),
        ]);

        foreach ($data['items'] as $i) {
            $order->items()->create([
                'product_id' => $i['product_id'],
                'qty' => $i['qty'],
                'unit_price' => $i['unit_price'],
                'total' => $i['unit_price'] * $i['qty'],
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Pedido manual #' . $order->id . ' creado.');
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.product', 'items.variant']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Orden actualizada.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'notify_status' => 'nullable|boolean',
        ]);

        $notify = $validated['notify_status'] ?? false;
        unset($validated['notify_status']);

        // Si la orden avanza a confirmada/enviada/entregada, el pago debe estar
        // marcado como pagado — no tiene sentido tener una orden enviada con pago pendiente.
        if (
            in_array($validated['status'], ['confirmed', 'shipped', 'delivered'], true)
            && $order->payment_status !== 'paid'
        ) {
            $validated['payment_status'] = 'paid';
        }

        $order->update($validated);

        if ($notify && $order->customer) {
            try {
                $order->load('items.product', 'items.variant', 'customer');
                Mail::to($order->customer->email)->send(new OrderStatusUpdate($order));
            } catch (\Throwable $e) {
                report($e);
                return redirect()->route('admin.orders.show', $order)
                    ->with('success', 'Estado actualizado, pero no se pudo enviar el correo.');
            }
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', $notify ? 'Estado actualizado y cliente notificado.' : 'Estado actualizado.');
    }

    public function verifyPayment(Order $order): RedirectResponse
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Pago verificado y orden confirmada.');
    }

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,processing,paid,failed,refunded',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Estado del pago actualizado a "' . $validated['payment_status'] . '".');
    }

    public function rejectPayment(Request $request, Order $order): RedirectResponse
    {
        // Delete the uploaded receipt
        if ($order->payment_receipt) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($order->payment_receipt);
        }

        $order->update([
            'payment_status' => 'pending',
            'payment_receipt' => null,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Comprobante rechazado. El cliente podrá subir uno nuevo.');
    }

    public function updateTracking(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_carrier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'tracking_url' => 'nullable|url|max:500',
            'notify_customer' => 'nullable|boolean',
        ]);

        $notify = $validated['notify_customer'] ?? false;
        unset($validated['notify_customer']);

        $order->update($validated);

        // Auto-set status to shipped if tracking is added and status is pending/confirmed
        if ($validated['tracking_number'] && in_array($order->status, ['pending', 'confirmed'])) {
            $order->update(['status' => 'shipped']);
        }

        if ($notify && $order->customer) {
            try {
                $order->load('items.product', 'items.variant', 'customer');
                Mail::to($order->customer->email)->send(new OrderShipped($order));
            } catch (\Throwable $e) {
                report($e);
                return redirect()->route('admin.orders.show', $order)
                    ->with('success', 'Guía actualizada, pero no se pudo enviar el correo.');
            }

            // Push notification al cliente (todos los navegadores donde esté
            // suscrito). No es bloqueante: si falla, el pedido queda avisado
            // por correo igual.
            try {
                $trackingUrl = $order->tracking_url
                    ?: route('order.track', ['tracking_token' => $order->tracking_token]);
                app(\App\Services\PushService::class)->sendToCustomer($order->customer_id, [
                    'title' => '📦 Tu pedido va en camino',
                    'body'  => 'Pedido #' . $order->id . ' — toca para ver el estado del envío.',
                    'url'   => $trackingUrl,
                    'tag'   => 'order-shipped-' . $order->id,
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', $notify ? 'Guía actualizada y cliente notificado.' : 'Guía actualizada.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Orden #' . $order->id . ' eliminada correctamente.');
    }

    public function exportCsv(): StreamedResponse
    {
        $orders = Order::with('customer')->latest()->get();

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Fecha', 'Cliente', 'Email', 'Total', 'Estado', 'Pago']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->customer->name,
                    $order->customer->email,
                    $order->total,
                    $order->status,
                    $order->payment_status,
                ]);
            }

            fclose($handle);
        }, 'ordenes-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
