@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@php
    // ─── Helpers ────────────────────────────────────────────────
    $cop = fn ($v) => '$' . number_format((float) $v, 0, ',', '.');
    $delta = function ($current, $previous) {
        if ($previous == 0) return $current == 0 ? 0 : 100;
        return round((($current - $previous) / $previous) * 100, 1);
    };
    $salesDelta   = $delta($sales_today, $sales_yesterday);
    $monthDelta   = $delta($sales_month, $sales_prev_month);
    $ordersDelta  = $delta($orders_today, $orders_yesterday);

    $statusLabels = [
        'pending'   => 'Pendiente',
        'paid'      => 'Pagado',
        'shipped'   => 'Enviado',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
        'refunded'  => 'Reembolsado',
    ];
    $statusHex = [
        'pending'   => '#F4C56C',
        'paid'      => '#7EC4A0',
        'shipped'   => '#8AB4F8',
        'delivered' => '#4B9E7D',
        'cancelled' => '#E88787',
        'refunded'  => '#B5B0A6',
    ];
    $paymentLabels = [
        'epayco'           => 'ePayco',
        'transfer'         => 'Transferencia',
        'cash_on_delivery' => 'Contraentrega',
        'manual'           => 'Manual',
    ];
    $paymentHex = [
        'epayco'           => '#D9B56D',
        'transfer'         => '#BE9A53',
        'cash_on_delivery' => '#EBCF90',
        'manual'           => '#8A7A55',
    ];

    // ── Payload de gráficas para JS ──
    $chartTrend = [
        'labels' => $trend_30d['labels'],
        'values' => $trend_30d['values'],
    ];
    $chartTop = [
        'labels' => $top_products_30d['labels'],
        'values' => $top_products_30d['values'],
    ];
    $chartStatus = [
        'labels' => array_values(array_map(fn ($k) => $statusLabels[$k] ?? $k, array_keys($status_breakdown))),
        'values' => array_values($status_breakdown),
        'colors' => array_values(array_map(fn ($k) => $statusHex[$k] ?? '#ccc', array_keys($status_breakdown))),
    ];
    $chartPayment = [
        'labels' => array_values(array_map(fn ($k) => $paymentLabels[$k] ?? $k, array_keys($payment_breakdown))),
        'values' => array_values($payment_breakdown),
        'colors' => array_values(array_map(fn ($k) => $paymentHex[$k] ?? '#ccc', array_keys($payment_breakdown))),
    ];
    $hasSales = array_sum($chartTrend['values']) > 0;
    $hasTop = count($chartTop['values']) > 0;
    $hasStatus = array_sum($chartStatus['values']) > 0;
    $hasPayment = array_sum($chartPayment['values']) > 0;

    $orderStatusColor = ['pending' => 'yellow', 'confirmed' => 'blue', 'shipped' => 'indigo', 'delivered' => 'green', 'cancelled' => 'red'];
    $orderStatusLabel = ['pending' => 'Pendiente', 'confirmed' => 'Confirmada', 'shipped' => 'Enviada', 'delivered' => 'Entregada', 'cancelled' => 'Cancelada'];
    $paymentStatusLabel = ['pending' => 'Pendiente', 'processing' => 'Procesando', 'paid' => 'Pagado', 'failed' => 'Fallido', 'refunded' => 'Reembolsado'];

    $hasAlerts = ($alert_overdue + $alert_wholesale + $alert_low_stock + $alert_receipts) > 0;
@endphp

@push('head')
<style>
    :root{
        --gold:#D9B56D;
        --gold-dark:#BE9A53;
        --gold-soft:#EBCF90;
        --gold-lite:#F7EFD9;
        --ink:#2E2A26;
        --cream:#FBF8F2;
    }
    .kpi-card{
        background:#fff;
        border-radius:20px;
        padding:22px 22px 20px;
        box-shadow:0 4px 20px -8px rgba(190,154,83,.22), 0 1px 2px rgba(0,0,0,.03);
        border:1px solid rgba(217,181,109,.18);
        position:relative;
        overflow:hidden;
        transition:transform .18s ease, box-shadow .18s ease;
    }
    .kpi-card:hover{
        transform:translateY(-2px);
        box-shadow:0 10px 28px -10px rgba(190,154,83,.35), 0 2px 6px rgba(0,0,0,.04);
    }
    .kpi-card::before{
        content:'';
        position:absolute;top:0;left:0;right:0;height:3px;
        background:linear-gradient(90deg, var(--gold-soft), var(--gold), var(--gold-dark));
    }
    .kpi-label{
        font-family:'Montserrat',sans-serif;
        font-size:11px;
        font-weight:600;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:#8A7A55;
    }
    .kpi-value{
        font-family:'Playfair Display', serif;
        font-size:34px;
        font-weight:600;
        color:var(--ink);
        line-height:1.15;
        margin-top:6px;
        letter-spacing:-.5px;
    }
    .kpi-icon{
        width:42px;height:42px;
        border-radius:14px;
        display:flex;align-items:center;justify-content:center;
        font-size:20px;
        background:linear-gradient(135deg, var(--gold-lite), #fff);
        border:1px solid rgba(217,181,109,.25);
        flex-shrink:0;
    }
    .delta{
        display:inline-flex;align-items:center;gap:4px;
        font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;
        padding:3px 8px;border-radius:999px;
        margin-top:10px;
    }
    .delta.up   { background:#E6F5EC; color:#1F7A44; }
    .delta.down { background:#FBE7E5; color:#B4241B; }
    .delta.flat { background:#F1EDE3; color:#7A6E52; }

    .panel{
        background:#fff;border-radius:20px;
        border:1px solid rgba(217,181,109,.18);
        box-shadow:0 4px 20px -12px rgba(190,154,83,.20), 0 1px 2px rgba(0,0,0,.03);
    }
    .panel-head{
        display:flex;align-items:center;justify-content:space-between;gap:12px;
        padding:18px 22px 12px;
    }
    .panel-title{
        font-family:'Playfair Display', serif;
        font-size:17px;font-weight:600;color:var(--ink);
    }
    .panel-sub{
        font-family:'Montserrat',sans-serif;font-size:11px;color:#8A7A55;
        letter-spacing:.08em;text-transform:uppercase;
    }
    .panel-body{ padding:6px 22px 20px; }
    .chart-wrap{ position:relative;height:280px; }
    .chart-wrap.dona{ height:260px; }
    .chart-empty{
        display:flex;align-items:center;justify-content:center;height:100%;
        color:#B5A97F;font-family:'Montserrat',sans-serif;font-size:13px;
        text-align:center;padding:20px;
    }

    .alert-card{
        border-radius:16px;padding:16px 18px;
        display:flex;align-items:center;gap:14px;
        transition:transform .15s ease;
    }
    .alert-card:hover{transform:translateY(-1px);}
    .alert-card.warn{ background:#FEF5E1; border:1px solid #F4C56C; color:#7A5A11; }
    .alert-card.danger{ background:#FBE7E5; border:1px solid #E88787; color:#7A1F17; }
    .alert-card.info { background:#EEF3FB; border:1px solid #B5CDF3; color:#2B4A7C; }
    .alert-num{
        font-family:'Playfair Display', serif;font-size:26px;font-weight:600;line-height:1;
    }
    .alert-txt{ font-family:'Montserrat',sans-serif;font-size:13px;font-weight:500; }

    .mini-card{
        background:#fff;border-radius:18px;padding:20px;
        border:1px solid rgba(217,181,109,.18);
        box-shadow:0 3px 14px -8px rgba(190,154,83,.20);
    }
    .mini-label{
        font-family:'Montserrat',sans-serif;font-size:11px;font-weight:600;
        color:#8A7A55;letter-spacing:.1em;text-transform:uppercase;
    }
    .mini-num{
        font-family:'Playfair Display', serif;font-size:30px;font-weight:600;
        color:var(--ink);margin-top:4px;line-height:1.15;
    }
    .mini-sub{
        font-family:'Montserrat',sans-serif;font-size:12px;color:#7A6E52;margin-top:4px;
    }

    .table-clean{ width:100%;border-collapse:collapse; }
    .table-clean thead th{
        font-family:'Montserrat',sans-serif;font-size:10.5px;font-weight:700;
        text-transform:uppercase;letter-spacing:.09em;color:#8A7A55;
        padding:12px 18px;text-align:left;
        border-bottom:1px solid rgba(217,181,109,.18);
        background:linear-gradient(180deg,#FBF8F2,#fff);
    }
    .table-clean tbody td{
        padding:14px 18px;font-size:13.5px;color:#3C362F;
        border-bottom:1px solid #F1EDE3;
    }
    .table-clean tbody tr:hover{ background:#FBF8F2; }
    .badge{
        display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;
        font-size:11px;font-weight:600;font-family:'Montserrat',sans-serif;letter-spacing:.02em;
    }

    .greeting{
        font-family:'Playfair Display', serif;font-size:22px;color:var(--ink);
        font-weight:500;letter-spacing:-.3px;
    }
    .greeting .accent{ color:var(--gold-dark); }
</style>
@endpush

@section('content')
    {{-- Saludo dorado --}}
    <div class="mb-6">
        <p class="greeting">Hola <span class="accent">{{ auth()->user()->name ?? 'admin' }}</span> — así va Áurea hoy</p>
        <p class="text-sm text-gray-500 mt-1" style="font-family:'Montserrat',sans-serif;">
            {{ ucfirst(\Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
        </p>
    </div>

    {{-- ─── KPIs Top ─── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        {{-- Ventas hoy --}}
        <div class="kpi-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="kpi-label">Ventas hoy</p>
                    <p class="kpi-value">{{ $cop($sales_today) }}</p>
                </div>
                <div class="kpi-icon">💰</div>
            </div>
            @php $cls = $salesDelta > 0 ? 'up' : ($salesDelta < 0 ? 'down' : 'flat'); @endphp
            <span class="delta {{ $cls }}">
                @if($salesDelta > 0) ↑ @elseif($salesDelta < 0) ↓ @else — @endif
                {{ number_format(abs($salesDelta), 1) }}%
                <span class="opacity-70">vs ayer</span>
            </span>
        </div>

        {{-- Ventas mes --}}
        <div class="kpi-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="kpi-label">Ventas este mes</p>
                    <p class="kpi-value">{{ $cop($sales_month) }}</p>
                </div>
                <div class="kpi-icon">📈</div>
            </div>
            @php $cls = $monthDelta > 0 ? 'up' : ($monthDelta < 0 ? 'down' : 'flat'); @endphp
            <span class="delta {{ $cls }}">
                @if($monthDelta > 0) ↑ @elseif($monthDelta < 0) ↓ @else — @endif
                {{ number_format(abs($monthDelta), 1) }}%
                <span class="opacity-70">vs mes anterior</span>
            </span>
        </div>

        {{-- Pedidos hoy --}}
        <div class="kpi-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="kpi-label">Pedidos hoy</p>
                    <p class="kpi-value">{{ $orders_today }}</p>
                </div>
                <div class="kpi-icon">🛍️</div>
            </div>
            @php $cls = $ordersDelta > 0 ? 'up' : ($ordersDelta < 0 ? 'down' : 'flat'); @endphp
            <span class="delta {{ $cls }}">
                @if($ordersDelta > 0) ↑ @elseif($ordersDelta < 0) ↓ @else — @endif
                {{ number_format(abs($ordersDelta), 1) }}%
                <span class="opacity-70">vs ayer</span>
            </span>
        </div>

        {{-- Ticket promedio --}}
        <div class="kpi-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="kpi-label">Ticket promedio (30d)</p>
                    <p class="kpi-value">{{ $cop($avg_ticket_30d) }}</p>
                </div>
                <div class="kpi-icon">✨</div>
            </div>
            <span class="delta flat">Últimos 30 días · pagados</span>
        </div>
    </div>

    {{-- ─── Necesitan tu atención ─── --}}
    @if($hasAlerts)
    <div class="mb-8">
        <p class="panel-sub mb-3">Necesitan tu atención</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($alert_overdue > 0)
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="alert-card danger">
                    <span style="font-size:26px;">🚚</span>
                    <div>
                        <div class="alert-num">{{ $alert_overdue }}</div>
                        <div class="alert-txt">sin despachar &gt; 48h</div>
                    </div>
                </a>
            @endif
            @if($alert_receipts > 0)
                <a href="{{ route('admin.orders.index', ['payment_method' => 'transfer']) }}" class="alert-card warn">
                    <span style="font-size:26px;">🧾</span>
                    <div>
                        <div class="alert-num">{{ $alert_receipts }}</div>
                        <div class="alert-txt">comprobantes por verificar</div>
                    </div>
                </a>
            @endif
            @if($alert_wholesale > 0)
                <a href="{{ route('admin.wholesale.index') }}" class="alert-card warn">
                    <span style="font-size:26px;">🏪</span>
                    <div>
                        <div class="alert-num">{{ $alert_wholesale }}</div>
                        <div class="alert-txt">solicitudes de mayoristas</div>
                    </div>
                </a>
            @endif
            @if($alert_low_stock > 0)
                <a href="{{ route('admin.products.index') }}" class="alert-card info">
                    <span style="font-size:26px;">📦</span>
                    <div>
                        <div class="alert-num">{{ $alert_low_stock }}</div>
                        <div class="alert-txt">productos con stock &lt; 5</div>
                    </div>
                </a>
            @endif
        </div>
    </div>
    @endif

    {{-- ─── Gráficas ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Tendencia --}}
        <div class="panel lg:col-span-2">
            <div class="panel-head">
                <div>
                    <p class="panel-title">Tendencia de ventas</p>
                    <p class="panel-sub">Últimos 30 días · pagados</p>
                </div>
                <span class="text-xs" style="color:#8A7A55;font-family:'Montserrat',sans-serif;">
                    {{ $cop(array_sum($chartTrend['values'])) }} totales
                </span>
            </div>
            <div class="panel-body">
                <div class="chart-wrap">
                    @if($hasSales)
                        <canvas id="chartTrend"></canvas>
                    @else
                        <div class="chart-empty">
                            <div>
                                <div style="font-size:32px;">🌱</div>
                                Aún no hay ventas registradas.<br>
                                <span class="opacity-60">Los pedidos pagados se verán aquí.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estados de pedido --}}
        <div class="panel">
            <div class="panel-head">
                <div>
                    <p class="panel-title">Estados de pedido</p>
                    <p class="panel-sub">Últimos 30 días</p>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-wrap dona">
                    @if($hasStatus)
                        <canvas id="chartStatus"></canvas>
                    @else
                        <div class="chart-empty">
                            <div>📊<br>Sin datos aún.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Top productos --}}
        <div class="panel lg:col-span-2">
            <div class="panel-head">
                <div>
                    <p class="panel-title">Top 5 productos más vendidos</p>
                    <p class="panel-sub">Últimos 30 días · unidades</p>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-wrap">
                    @if($hasTop)
                        <canvas id="chartTop"></canvas>
                    @else
                        <div class="chart-empty">
                            <div>🌟<br>Ningún producto vendido todavía.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Métodos de pago --}}
        <div class="panel">
            <div class="panel-head">
                <div>
                    <p class="panel-title">Métodos de pago</p>
                    <p class="panel-sub">Últimos 30 días · pagados</p>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-wrap dona">
                    @if($hasPayment)
                        <canvas id="chartPayment"></canvas>
                    @else
                        <div class="chart-empty">
                            <div>💳<br>Sin pagos aún.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Métricas de cliente ─── --}}
    <div class="mb-8">
        <p class="panel-sub mb-3">Métricas de cliente</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="mini-card">
                <p class="mini-label">👥 Clientes</p>
                <p class="mini-num">{{ number_format($customers_total, 0, ',', '.') }}</p>
                <p class="mini-sub">+{{ $customers_new_month }} este mes</p>
            </div>
            <div class="mini-card">
                <p class="mini-label">💖 NPS (30d)</p>
                <p class="mini-num">
                    @if($nps_score === null)
                        —
                    @else
                        {{ $nps_score > 0 ? '+' . $nps_score : $nps_score }}
                    @endif
                </p>
                <p class="mini-sub">
                    @if($nps_responses > 0)
                        {{ $nps_responses }} respuesta{{ $nps_responses === 1 ? '' : 's' }}
                    @else
                        sin respuestas aún
                    @endif
                </p>
            </div>
            <div class="mini-card">
                <p class="mini-label">🔔 Push</p>
                <p class="mini-num">{{ number_format($push_subs, 0, ',', '.') }}</p>
                <p class="mini-sub">suscriptores activos</p>
            </div>
            <div class="mini-card">
                <p class="mini-label">📧 Newsletter</p>
                <p class="mini-num">{{ number_format($newsletter_subs, 0, ',', '.') }}</p>
                <p class="mini-sub">emails capturados</p>
            </div>
        </div>
    </div>

    {{-- ─── Últimos pedidos ─── --}}
    <div class="panel overflow-hidden">
        <div class="panel-head">
            <div>
                <p class="panel-title">Últimos pedidos</p>
                <p class="panel-sub">Los 10 más recientes</p>
            </div>
            <a href="{{ route('admin.orders.index') }}"
               class="text-sm font-medium"
               style="color:var(--gold-dark);font-family:'Montserrat',sans-serif;">
                Ver todos →
            </a>
        </div>
        @if($recentOrders->isEmpty())
            <div class="p-10 text-center" style="color:#8A7A55;font-family:'Montserrat',sans-serif;">
                <div class="text-4xl mb-2">🕊️</div>
                No hay pedidos todavía. ¡Ya llegarán!
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Método</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            @php
                                $c = $orderStatusColor[$order->status] ?? 'gray';
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       style="color:var(--gold-dark);font-weight:600;">
                                        #{{ $order->id }}
                                    </a>
                                </td>
                                <td>{{ $order->customer->name ?? '—' }}</td>
                                <td style="font-weight:600;">{{ $cop($order->total) }}</td>
                                <td>
                                    <span class="badge bg-{{ $c }}-100 text-{{ $c }}-800">
                                        {{ $orderStatusLabel[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs" style="color:#7A6E52;">
                                        {{ $paymentStatusLabel[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs" style="color:#7A6E52;">
                                        {{ $paymentLabels[$order->payment_method] ?? ($order->payment_method ?? '—') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs" style="color:#7A6E52;">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
(function(){
    if (typeof Chart === 'undefined') return;

    // ─── Datos desde PHP ───
    const trendData   = @json($chartTrend);
    const topData     = @json($chartTop);
    const statusData  = @json($chartStatus);
    const paymentData = @json($chartPayment);
    const hasSales    = @json($hasSales);
    const hasTop      = @json($hasTop);
    const hasStatus   = @json($hasStatus);
    const hasPayment  = @json($hasPayment);

    // ─── Formatos ───
    const cop = (v) => '$' + Math.round(v).toLocaleString('es-CO');

    // Estilo base
    Chart.defaults.font.family = 'Montserrat, sans-serif';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#7A6E52';
    Chart.defaults.plugins.tooltip.backgroundColor = '#2E2A26';
    Chart.defaults.plugins.tooltip.titleColor = '#EBCF90';
    Chart.defaults.plugins.tooltip.bodyColor = '#FBF8F2';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 10;
    Chart.defaults.plugins.tooltip.titleFont = { size: 12, weight: '600' };

    // ─── 1) Tendencia ventas 30 días ───
    if (hasSales) {
        const ctx = document.getElementById('chartTrend').getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(217,181,109,.45)');
        grad.addColorStop(1, 'rgba(217,181,109,0)');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Ventas',
                    data: trendData.values,
                    borderColor: '#BE9A53',
                    backgroundColor: grad,
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#BE9A53',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + cop(ctx.parsed.y),
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxTicksLimit: 8, autoSkip: true },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(190,154,83,.08)', drawBorder: false },
                        ticks: {
                            callback: (v) => cop(v).replace('$', '$'),
                        },
                    },
                },
            },
        });
    }

    // ─── 2) Top 5 productos (barras horizontales) ───
    if (hasTop) {
        const ctx = document.getElementById('chartTop').getContext('2d');
        const goldPalette = ['#BE9A53', '#D9B56D', '#E3C285', '#EBCF90', '#F1DAA8'];
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: topData.labels,
                datasets: [{
                    label: 'Unidades',
                    data: topData.values,
                    backgroundColor: topData.values.map((_, i) => goldPalette[i % goldPalette.length]),
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 22,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + ctx.parsed.x + ' unidades',
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(190,154,83,.08)', drawBorder: false },
                        ticks: { precision: 0 },
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                return label && label.length > 22 ? label.slice(0, 22) + '…' : label;
                            },
                        },
                    },
                },
            },
        });
    }

    // ─── 3) Estados de pedido (dona) ───
    if (hasStatus) {
        const ctx = document.getElementById('chartStatus').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.values,
                    backgroundColor: statusData.colors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, boxHeight: 10, padding: 12, font: { size: 11 } },
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + ctx.label + ': ' + ctx.parsed,
                        },
                    },
                },
            },
        });
    }

    // ─── 4) Métodos de pago (dona) ───
    if (hasPayment) {
        const ctx = document.getElementById('chartPayment').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: paymentData.labels,
                datasets: [{
                    data: paymentData.values,
                    backgroundColor: paymentData.colors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, boxHeight: 10, padding: 12, font: { size: 11 } },
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ' ' + ctx.label + ': ' + ctx.parsed + ' pedido' + (ctx.parsed === 1 ? '' : 's'),
                        },
                    },
                },
            },
        });
    }
})();
</script>
@endpush
