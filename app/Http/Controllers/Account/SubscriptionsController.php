<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionCancelled;
use App\Mail\SubscriptionPaused;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SubscriptionsController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function index(): View
    {
        $customer = $this->customer();
        $subscriptions = $customer->subscriptions()
            ->with(['plan.products', 'deliveries.order'])
            ->latest()
            ->get();

        return view('account.subscriptions.index', compact('customer', 'subscriptions'));
    }

    public function show(Subscription $subscription): View
    {
        abort_unless($subscription->customer_id === $this->customer()->id, 403);
        $subscription->load(['plan.products', 'deliveries.order']);

        return view('account.subscriptions.show', compact('subscription'));
    }

    public function pause(Request $request, Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->customer_id === $this->customer()->id, 403);
        abort_unless($subscription->status === 'active', 422);

        $data = $request->validate([
            'pause_reason' => 'nullable|string|max:255',
        ]);

        $subscription->pause($data['pause_reason'] ?? null);

        try {
            Mail::to($this->customer()->email)->send(new SubscriptionPaused($subscription));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Suscripción pausada. Puedes reanudarla cuando quieras 💛');
    }

    public function resume(Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->customer_id === $this->customer()->id, 403);
        abort_unless($subscription->status === 'paused', 422);

        $subscription->resume();

        return back()->with('success', 'Suscripción reanudada. Próxima entrega: ' .
            $subscription->fresh()->next_delivery_at->translatedFormat('l j \d\e F'));
    }

    public function cancel(Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->customer_id === $this->customer()->id, 403);
        abort_if($subscription->status === 'cancelled', 422);

        $subscription->cancel();

        try {
            Mail::to($this->customer()->email)->send(new SubscriptionCancelled($subscription));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('account.subscriptions')
            ->with('success', 'Suscripción cancelada. Gracias por haber sido parte de tu ritual 💛');
    }

    public function updateAddress(Request $request, Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->customer_id === $this->customer()->id, 403);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'required|string|max:500',
            'city'     => 'required|string|max:100',
            'state'    => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:10',
        ]);

        $subscription->update(['shipping_address_json' => $data]);

        return back()->with('success', 'Dirección de entrega actualizada.');
    }
}
