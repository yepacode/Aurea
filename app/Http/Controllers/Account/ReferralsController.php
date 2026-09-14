<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Vista del cliente para el programa "Recomienda y gana".
 * Muestra el código único, link listo para compartir y el listado de amigas
 * ya referidas con su estado.
 */
class ReferralsController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        // Garantía por si un cliente muy viejo no tiene código (backfill perezoso).
        if (empty($customer->referral_code)) {
            $customer->referral_code = \App\Models\Customer::generateUniqueReferralCode($customer->name ?? '');
            $customer->save();
        }

        $shareUrl = url('/?ref=' . $customer->referral_code);

        // Mensaje predefinido para WhatsApp (URL-encoded).
        $whatsAppText = "¡Hola! 💛 Te recomiendo Belleza Áurea, tienen cosmética increíble. "
            . "Regístrate con mi código {$customer->referral_code} y llévate 500 puntos de bienvenida: {$shareUrl}";

        $whatsAppUrl = 'https://wa.me/?text=' . rawurlencode($whatsAppText);

        // Listado de amigas referidas.
        $referrals = $customer->referralsMade()
            ->with('referred:id,name,email')
            ->latest()
            ->paginate(15);

        // Métricas del hero: totales + puntos ganados por referidas ya completadas.
        $totalReferrals     = $customer->referralsMade()->count();
        $completedReferrals = $customer->referralsMade()->where('status', 'completed')->count();
        $pointsEarned       = (int) $customer->referralsMade()
            ->where('status', 'completed')
            ->sum('reward_referrer_points');

        return view('account.referrals', compact(
            'customer',
            'shareUrl',
            'whatsAppUrl',
            'referrals',
            'totalReferrals',
            'completedReferrals',
            'pointsEarned',
        ));
    }
}
