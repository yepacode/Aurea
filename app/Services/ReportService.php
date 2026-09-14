<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;

/**
 * ReportService — genera reportes CSV en streaming (sin cargar
 * todo en memoria). Cada método escribe filas directamente sobre
 * el handle recibido, empezando por la cabecera.
 */
class ReportService
{
    // ────────────────────────────────────────────────────────────
    // 1. Pedidos
    // ────────────────────────────────────────────────────────────

    public function orders(array $filters, $out): void
    {
        fputcsv($out, [
            'ID', 'Fecha', 'Cliente', 'Email', 'Teléfono',
            'Subtotal', 'Envío', 'Descuento', 'Total',
            'Método pago', 'Estado pago', 'Estado pedido',
            'Transportadora', 'Zona', 'Tracking', 'URL tracking',
            'Referencia pago', 'Cupón', 'Notas',
        ]);

        $q = Order::query()->with('customer');

        [$from, $to] = $this->normalizeDates($filters);
        if ($from) $q->where('created_at', '>=', $from);
        if ($to)   $q->where('created_at', '<=', $to);

        if (! empty($filters['status']))         $q->where('status', $filters['status']);
        if (! empty($filters['payment_status'])) $q->where('payment_status', $filters['payment_status']);
        if (! empty($filters['payment_method'])) $q->where('payment_method', $filters['payment_method']);

        $q->latest('id')->chunk(500, function ($orders) use ($out) {
            foreach ($orders as $o) {
                $discount = (float) $o->discount_amount
                          + (float) $o->discount_2x1
                          + (float) $o->discount_coupon;

                fputcsv($out, [
                    $o->id,
                    optional($o->created_at)?->format('Y-m-d H:i'),
                    optional($o->customer)?->name ?: '—',
                    optional($o->customer)?->email ?: '',
                    optional($o->customer)?->phone ?: '',
                    $this->money($o->subtotal),
                    $this->money($o->shipping),
                    $this->money($discount),
                    $this->money($o->total),
                    $o->payment_method ?: '',
                    $o->payment_status ?: '',
                    $o->status ?: '',
                    $o->shipping_carrier ?: '',
                    $o->shipping_zone_name ?: '',
                    $o->tracking_number ?: '',
                    $o->tracking_url ?: '',
                    $o->payment_reference ?: '',
                    $o->discount_code ?: '',
                    $this->cleanText($o->notes),
                ]);
            }
        });
    }

    // ────────────────────────────────────────────────────────────
    // 2. Ventas por producto
    // ────────────────────────────────────────────────────────────

    public function salesByProduct(array $filters, $out): void
    {
        fputcsv($out, [
            'Producto ID', 'Nombre', 'Categoría', 'Marca',
            'Unidades vendidas', 'Ingreso total', 'Precio promedio',
        ]);

        [$from, $to] = $this->normalizeDates($filters);

        $rows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->where('orders.payment_status', 'paid')
            ->when($from, fn($q) => $q->where('orders.created_at', '>=', $from))
            ->when($to,   fn($q) => $q->where('orders.created_at', '<=', $to))
            ->groupBy('order_items.product_id', 'products.name', 'categories.name', 'brands.name')
            ->orderByRaw('SUM(order_items.qty) DESC')
            ->selectRaw('order_items.product_id as pid,
                         products.name as pname,
                         categories.name as cname,
                         brands.name as bname,
                         SUM(order_items.qty) as units,
                         SUM(order_items.total) as revenue,
                         AVG(order_items.unit_price) as avg_price')
            ->cursor();

        foreach ($rows as $r) {
            fputcsv($out, [
                $r->pid,
                $r->pname ?: '(producto eliminado)',
                $r->cname ?: '',
                $r->bname ?: '',
                (int) $r->units,
                $this->money($r->revenue),
                $this->money($r->avg_price),
            ]);
        }
    }

    // ────────────────────────────────────────────────────────────
    // 3. Clientes
    // ────────────────────────────────────────────────────────────

    public function customers(array $filters, $out): void
    {
        fputcsv($out, [
            'ID', 'Nombre', 'Email', 'Teléfono', 'Ciudad', 'Depto',
            'Registro', 'Pedidos pagados', 'Total gastado', 'Última compra',
            'Mayorista', 'Estado mayorista', 'Empresa', 'NIT',
            'Puntos actuales', 'Puntos ganados', 'Puntos canjeados',
        ]);

        Customer::query()
            ->orderBy('id')
            ->chunk(300, function ($customers) use ($out) {
                foreach ($customers as $c) {
                    $paid   = $c->orders()->where('payment_status', 'paid');
                    $count  = (int) (clone $paid)->count();
                    $sum    = (float) (clone $paid)->sum('total');
                    $last   = (clone $paid)->latest('created_at')->value('created_at');

                    fputcsv($out, [
                        $c->id,
                        $c->name ?: '',
                        $c->email,
                        $c->phone ?: '',
                        $c->city ?: '',
                        $c->state ?: '',
                        optional($c->created_at)?->format('Y-m-d'),
                        $count,
                        $this->money($sum),
                        $last ? Carbon::parse($last)->format('Y-m-d') : '',
                        $c->is_wholesaler ? 'Sí' : 'No',
                        $c->wholesaler_status ?: '',
                        $c->wholesaler_company_name ?: '',
                        $c->wholesaler_nit ?: '',
                        $c->pointsBalance(),
                        $c->pointsEarnedTotal(),
                        $c->pointsRedeemedTotal(),
                    ]);
                }
            });
    }

    // ────────────────────────────────────────────────────────────
    // 4. Inventario bajo
    // ────────────────────────────────────────────────────────────

    public function lowStock(array $filters, $out): void
    {
        fputcsv($out, [
            'Producto ID', 'Código interno', 'Nombre', 'Categoría', 'Marca',
            'Stock actual', 'Umbral', 'Precio', 'Última venta',
        ]);

        $threshold = isset($filters['threshold'])
            ? max(0, (int) $filters['threshold'])
            : 10;

        Product::query()
            ->with(['category', 'brand'])
            ->where('is_active', true)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->chunk(300, function ($products) use ($out, $threshold) {
                foreach ($products as $p) {
                    $lastSale = OrderItem::query()
                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                        ->where('order_items.product_id', $p->id)
                        ->where('orders.payment_status', 'paid')
                        ->latest('orders.created_at')
                        ->value('orders.created_at');

                    fputcsv($out, [
                        $p->id,
                        $p->internal_code ?: '',
                        $p->name,
                        optional($p->category)?->name ?: '',
                        optional($p->brand)?->name ?: '',
                        (int) $p->stock,
                        $threshold,
                        $this->money($p->price),
                        $lastSale ? Carbon::parse($lastSale)->format('Y-m-d') : '',
                    ]);
                }
            });
    }

    // ────────────────────────────────────────────────────────────
    // 5. Movimientos de puntos (fidelización)
    // ────────────────────────────────────────────────────────────

    public function loyalty(array $filters, $out): void
    {
        fputcsv($out, [
            'Fecha', 'Cliente', 'Email', 'Tipo', 'Puntos',
            'Pedido asociado', 'Nota', 'Expira',
        ]);

        [$from, $to] = $this->normalizeDates($filters);

        LoyaltyPoint::query()
            ->with('customer')
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to,   fn($q) => $q->where('created_at', '<=', $to))
            ->when(! empty($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->latest('id')
            ->chunk(500, function ($points) use ($out) {
                foreach ($points as $p) {
                    fputcsv($out, [
                        optional($p->created_at)?->format('Y-m-d H:i'),
                        optional($p->customer)?->name ?: '',
                        optional($p->customer)?->email ?: '',
                        $p->type ?: '',
                        (int) $p->points,
                        $p->order_id ?: '',
                        $this->cleanText($p->note),
                        optional($p->expires_at)?->format('Y-m-d') ?: '',
                    ]);
                }
            });
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function normalizeDates(array $filters): array
    {
        $from = ! empty($filters['from']) ? $this->parseDate($filters['from'], false) : null;
        $to   = ! empty($filters['to'])   ? $this->parseDate($filters['to'],   true)  : null;
        return [$from, $to];
    }

    private function parseDate(string $s, bool $endOfDay): ?Carbon
    {
        try {
            $d = Carbon::parse($s);
            return $endOfDay ? $d->endOfDay() : $d->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Formato de dinero sin miles (CSV-friendly). */
    private function money($v): string
    {
        return number_format((float) ($v ?? 0), 2, '.', '');
    }

    /** Limpia saltos de línea que rompen el CSV visualmente. */
    private function cleanText(?string $text): string
    {
        if (! $text) return '';
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}
