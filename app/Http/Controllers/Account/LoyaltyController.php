<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Vista del cliente para su programa de puntos.
 * Solo lectura por ahora: balance, totales y movimientos. El canje llega
 * en el próximo sprint.
 */
class LoyaltyController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        // Balance y totales — usan los helpers del modelo Customer.
        $balance         = $customer->pointsBalance();
        $earnedTotal     = $customer->pointsEarnedTotal();
        $redeemedTotal   = $customer->pointsRedeemedTotal();

        // Puntos vigentes que vencen dentro de 30 días (aviso de urgencia).
        $expiringSoon = (int) $customer->loyaltyPoints()
            ->active()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->where('points', '>', 0)
            ->sum('points');

        // Historial paginado 20/pág, más nuevo primero.
        $movements = $customer->loyaltyPoints()
            ->with('order:id')
            ->latest()
            ->paginate(20);

        return view('account.loyalty', compact(
            'customer',
            'balance',
            'earnedTotal',
            'redeemedTotal',
            'expiringSoon',
            'movements',
        ));
    }
}
