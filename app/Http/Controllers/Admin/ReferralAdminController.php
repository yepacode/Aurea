<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Referral;
use Illuminate\View\View;

/**
 * Panel de tracking del programa "Recomienda y gana".
 * Solo lectura: métricas rápidas y últimas referidas.
 */
class ReferralAdminController extends Controller
{
    public function index(): View
    {
        $total     = Referral::count();
        $completed = Referral::where('status', 'completed')->count();
        $pending   = Referral::where('status', 'pending')->count();
        $rate      = $total > 0 ? round(($completed / $total) * 100, 1) : 0.0;

        // Top referrer (por cantidad de referidas completadas).
        $topReferrer = Customer::withCount([
                'referralsMade as completed_count' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->orderByDesc('completed_count')
            ->having('completed_count', '>', 0)
            ->first();

        $referrals = Referral::with(['referrer:id,name,email', 'referred:id,name,email'])
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.referrals.index', compact(
            'total',
            'completed',
            'pending',
            'rate',
            'topReferrer',
            'referrals',
        ));
    }
}
