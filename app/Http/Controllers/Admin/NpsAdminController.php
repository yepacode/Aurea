<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NpsResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NpsAdminController extends Controller
{
    public function index(Request $request): View
    {
        // Filtros de fecha (aplican a responded_at, que es cuando la respuesta cuenta para el score).
        $from = $request->filled('from')
            ? \Illuminate\Support\Carbon::parse($request->input('from'))->startOfDay()
            : now()->subDays(90)->startOfDay();
        $to = $request->filled('to')
            ? \Illuminate\Support\Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        // Solo respuestas ya contestadas dentro del rango cuentan para el score.
        $answered = NpsResponse::query()
            ->whereNotNull('responded_at')
            ->whereBetween('responded_at', [$from, $to])
            ->get();

        $total       = $answered->count();
        $promoters   = $answered->filter(fn ($r) => $r->score >= 9)->count();
        $passives    = $answered->filter(fn ($r) => $r->score >= 7 && $r->score <= 8)->count();
        $detractors  = $answered->filter(fn ($r) => $r->score <= 6)->count();

        $promoterPct  = $total > 0 ? round($promoters  / $total * 100, 1) : 0;
        $passivePct   = $total > 0 ? round($passives   / $total * 100, 1) : 0;
        $detractorPct = $total > 0 ? round($detractors / $total * 100, 1) : 0;

        // NPS score = %promotores - %detractores (redondeado a entero).
        $npsScore = $total > 0 ? (int) round($promoterPct - $detractorPct) : null;

        // Distribución 0..10 para el gráfico de barras.
        $distribution = collect(range(0, 10))->mapWithKeys(function ($n) use ($answered) {
            return [$n => $answered->where('score', $n)->count()];
        })->all();

        // Últimas 50 respuestas (dentro del rango filtrado).
        $recent = NpsResponse::query()
            ->whereNotNull('responded_at')
            ->whereBetween('responded_at', [$from, $to])
            ->with(['order', 'customer'])
            ->orderByDesc('responded_at')
            ->limit(50)
            ->get();

        // Métricas globales de envío para dar contexto (fuera del rango filtrado).
        $sentTotal      = NpsResponse::whereNotNull('sent_at')->count();
        $respondedTotal = NpsResponse::whereNotNull('responded_at')->count();
        $responseRate   = $sentTotal > 0 ? round($respondedTotal / $sentTotal * 100, 1) : 0;

        return view('admin.nps.index', compact(
            'from', 'to',
            'total', 'promoters', 'passives', 'detractors',
            'promoterPct', 'passivePct', 'detractorPct',
            'npsScore', 'distribution', 'recent',
            'sentTotal', 'respondedTotal', 'responseRate',
        ));
    }
}
