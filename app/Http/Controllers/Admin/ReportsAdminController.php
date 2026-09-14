<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ReportsAdminController — expone 5 reportes descargables (CSV
 * en streaming) usados desde el panel:
 *   - Pedidos
 *   - Ventas por producto
 *   - Clientes
 *   - Inventario bajo
 *   - Movimientos de puntos (fidelización)
 *
 * Cada método recibe filtros desde la querystring, valida lo
 * mínimo y delega la escritura a `ReportService`. La respuesta
 * es `StreamedResponse` con BOM UTF-8 para que Excel abra los
 * acentos correctamente.
 */
class ReportsAdminController extends Controller
{
    public function __construct(private readonly ReportService $service) {}

    public function orders(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'from'           => ['nullable', 'date'],
            'to'             => ['nullable', 'date'],
            'status'         => ['nullable', 'string', 'max:32'],
            'payment_status' => ['nullable', 'string', 'max:32'],
            'payment_method' => ['nullable', 'string', 'max:32'],
        ]);

        return $this->stream(
            $this->fileName('pedidos'),
            fn($out) => $this->service->orders($filters, $out),
        );
    }

    public function salesByProduct(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
        ]);

        return $this->stream(
            $this->fileName('ventas-por-producto'),
            fn($out) => $this->service->salesByProduct($filters, $out),
        );
    }

    public function customers(Request $request): StreamedResponse
    {
        return $this->stream(
            $this->fileName('clientes'),
            fn($out) => $this->service->customers([], $out),
        );
    }

    public function lowStock(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'threshold' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        return $this->stream(
            $this->fileName('inventario-bajo'),
            fn($out) => $this->service->lowStock($filters, $out),
        );
    }

    public function loyalty(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
            'type' => ['nullable', 'string', 'in:earned,redeemed,expired,adjusted'],
        ]);

        return $this->stream(
            $this->fileName('puntos-fidelizacion'),
            fn($out) => $this->service->loyalty($filters, $out),
        );
    }

    // ────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────

    private function stream(string $fileName, \Closure $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($writer) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 → Excel abre acentos y ñ correctamente.
            fputs($out, "\xEF\xBB\xBF");
            $writer($out);
            fclose($out);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
        ]);
    }

    private function fileName(string $slug): string
    {
        return "aurea-{$slug}-" . now()->format('Y-m-d-His') . '.csv';
    }
}
