<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\NpsResponse;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Dashboard administrativo con KPIs, gráficas y alertas operativas.
 *
 * - Todas las agregaciones "pesadas" se cachean 5 min (CACHE_STORE=database).
 * - Consultas se hacen sobre columnas indexadas o cortas (últimos 30d).
 * - Empty states: si no hay data, se renderizan ceros / listas vacías
 *   sin romper la vista (json_encode devuelve arrays vacíos, ChartJS los tolera).
 */
class DashboardAdminController extends Controller
{
    /** Ventana estándar de análisis (en días). */
    private const WINDOW_DAYS = 30;

    /** TTL de la caché para las agregaciones. */
    private const CACHE_TTL = 300; // 5 min

    /** Umbral de "sin despachar" en horas. */
    private const OVERDUE_HOURS = 48;

    /** Stock bajo: producto activo con menos de N unidades. */
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(): View
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startPrevMonth = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $endPrevMonth = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        $windowStart = Carbon::now()->subDays(self::WINDOW_DAYS - 1)->startOfDay();

        $data = Cache::remember('admin.dashboard.v1', self::CACHE_TTL, function () use (
            $today, $yesterday, $startOfMonth, $startPrevMonth, $endPrevMonth, $windowStart
        ) {
            return [
                // ── KPIs top ──
                'sales_today'        => $this->paidSalesBetween($today->copy()->startOfDay(), $today->copy()->endOfDay()),
                'sales_yesterday'    => $this->paidSalesBetween($yesterday->copy()->startOfDay(), $yesterday->copy()->endOfDay()),
                'sales_month'        => $this->paidSalesBetween($startOfMonth, Carbon::now()),
                'sales_prev_month'   => $this->paidSalesBetween($startPrevMonth, $endPrevMonth),
                'orders_today'       => Order::whereDate('created_at', $today)->count(),
                'orders_yesterday'   => Order::whereDate('created_at', $yesterday)->count(),
                'avg_ticket_30d'     => $this->avgTicket($windowStart),

                // ── Gráficas ──
                'trend_30d'          => $this->salesTrend($windowStart),
                'top_products_30d'   => $this->topProducts($windowStart, 5),
                'status_breakdown'   => $this->statusBreakdown($windowStart),
                'payment_breakdown'  => $this->paymentBreakdown($windowStart),

                // ── Alertas operativas ──
                'alert_overdue'      => $this->overdueOrders(),
                'alert_wholesale'    => Customer::where('wholesaler_status', 'pending')->count(),
                'alert_low_stock'    => Product::where('is_active', true)
                                        ->where('stock', '<', self::LOW_STOCK_THRESHOLD)
                                        ->count(),
                'alert_receipts'     => $this->pendingReceipts(),

                // ── Métricas de cliente ──
                'customers_total'    => Customer::count(),
                'customers_new_month'=> Customer::where('created_at', '>=', $startOfMonth)->count(),
                'nps_score'          => $this->npsScore($windowStart),
                'nps_responses'      => NpsResponse::whereNotNull('responded_at')
                                        ->where('responded_at', '>=', $windowStart)
                                        ->count(),
                'push_subs'          => $this->countIfTable('push_subscriptions'),
                'newsletter_subs'    => Lead::count(),
            ];
        });

        // Los últimos pedidos no se cachean (deben verse fresh).
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', array_merge($data, [
            'recentOrders' => $recentOrders,
        ]));
    }

    // ────────────────────────────────────────────────────────
    // Helpers de agregación
    // ────────────────────────────────────────────────────────

    private function paidSalesBetween(Carbon $from, Carbon $to): float
    {
        return (float) Order::whereBetween('created_at', [$from, $to])
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    private function avgTicket(Carbon $from): float
    {
        return (float) Order::where('created_at', '>=', $from)
            ->where('payment_status', 'paid')
            ->avg('total');
    }

    /**
     * Serie diaria de ventas (últimos 30 días). Rellena días sin ventas con 0.
     */
    private function salesTrend(Carbon $from): array
    {
        $rows = Order::selectRaw('DATE(created_at) AS d, SUM(total) AS total, COUNT(*) AS orders')
            ->where('created_at', '>=', $from)
            ->where('payment_status', 'paid')
            ->groupBy('d')
            ->pluck('total', 'd')
            ->toArray();

        $labels = [];
        $values = [];
        for ($i = self::WINDOW_DAYS - 1; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->toDateString();
            $labels[] = Carbon::parse($day)->format('d M');
            $values[] = (float) ($rows[$day] ?? 0);
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Top N productos por unidades vendidas (pedidos pagados) en la ventana.
     */
    private function topProducts(Carbon $from, int $limit = 5): array
    {
        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $from)
            ->groupBy('order_items.product_id', 'products.name')
            ->select('products.name', DB::raw('SUM(order_items.qty) AS units'))
            ->orderByDesc('units')
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'values' => $rows->pluck('units')->map(fn ($v) => (int) $v)->toArray(),
        ];
    }

    /**
     * Distribución de pedidos por status (últimos 30 días).
     * Devuelve un array asociativo status => count con TODOS los estados
     * (aun los que están en 0) para que la dona sea consistente.
     */
    private function statusBreakdown(Carbon $from): array
    {
        // Los "status" reales del sistema son: pending, confirmed, shipped, delivered, cancelled.
        // Se muestran los del enunciado (pending, paid, shipped, delivered, cancelled, refunded)
        // combinando order.status + payment_status donde tiene sentido.
        $counts = [
            'pending'   => 0,
            'paid'      => 0,   // confirmed + payment=paid pero aún sin envío
            'shipped'   => 0,
            'delivered' => 0,
            'cancelled' => 0,
            'refunded'  => 0,
        ];

        $rows = Order::selectRaw('status, payment_status, COUNT(*) AS c')
            ->where('created_at', '>=', $from)
            ->groupBy('status', 'payment_status')
            ->get();

        foreach ($rows as $r) {
            $s = $r->status;
            $ps = $r->payment_status;
            $c = (int) $r->c;

            if ($ps === 'refunded') { $counts['refunded'] += $c; continue; }
            if ($s === 'cancelled') { $counts['cancelled'] += $c; continue; }
            if ($s === 'delivered') { $counts['delivered'] += $c; continue; }
            if ($s === 'shipped')   { $counts['shipped']   += $c; continue; }
            if ($s === 'confirmed' && $ps === 'paid') { $counts['paid'] += $c; continue; }
            $counts['pending'] += $c;
        }

        return $counts;
    }

    /**
     * Distribución por método de pago (pedidos pagados) en la ventana.
     */
    private function paymentBreakdown(Carbon $from): array
    {
        $rows = Order::selectRaw('COALESCE(payment_method, "manual") AS method, COUNT(*) AS c')
            ->where('created_at', '>=', $from)
            ->where('payment_status', 'paid')
            ->groupBy('method')
            ->pluck('c', 'method')
            ->toArray();

        // Aseguramos las 3 keys esperadas + cualquier otra que aparezca.
        $base = [
            'epayco'           => 0,
            'transfer'         => 0,
            'cash_on_delivery' => 0,
        ];
        return array_merge($base, array_map('intval', $rows));
    }

    /**
     * Pedidos pagados pero sin despachar (status pending|confirmed) con más de N horas.
     */
    private function overdueOrders(): int
    {
        return Order::whereIn('status', ['pending', 'confirmed'])
            ->where('payment_status', 'paid')
            ->where('created_at', '<', Carbon::now()->subHours(self::OVERDUE_HOURS))
            ->count();
    }

    /**
     * Comprobantes de transferencia recibidos pero aún NO verificados.
     */
    private function pendingReceipts(): int
    {
        return Order::where('payment_method', 'transfer')
            ->whereIn('payment_status', ['pending', 'processing'])
            ->whereNotNull('payment_receipt')
            ->count();
    }

    /**
     * NPS ((promoters - detractors) / total) * 100. Sólo respondidos en la ventana.
     * Devuelve null si aún no hay respuestas.
     */
    private function npsScore(Carbon $from): ?int
    {
        $rows = NpsResponse::selectRaw('
                SUM(CASE WHEN score >= 9 THEN 1 ELSE 0 END) AS promoters,
                SUM(CASE WHEN score BETWEEN 0 AND 6 THEN 1 ELSE 0 END) AS detractors,
                COUNT(*) AS total
            ')
            ->whereNotNull('responded_at')
            ->where('responded_at', '>=', $from)
            ->first();

        $total = (int) ($rows->total ?? 0);
        if ($total === 0) return null;

        $promoters = (int) $rows->promoters;
        $detractors = (int) $rows->detractors;

        return (int) round((($promoters - $detractors) / $total) * 100);
    }

    /**
     * Cuenta filas de una tabla si existe, si no devuelve 0. Evita crash cuando
     * un módulo aún no está migrado en el entorno.
     */
    private function countIfTable(string $table): int
    {
        return Schema::hasTable($table) ? (int) DB::table($table)->count() : 0;
    }
}
