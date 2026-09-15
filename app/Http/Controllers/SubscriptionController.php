<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionCreated;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /** Landing pública "Rituales mensuales". */
    public function index(): View
    {
        $plans = SubscriptionPlan::active()->ordered()
            ->with(['products' => fn ($q) => $q->select('products.id', 'products.name', 'products.slug', 'products.price', 'products.images')])
            ->get();

        return view('storefront.subscriptions.index', compact('plans'));
    }

    /** Ficha detallada de un plan. */
    public function show(SubscriptionPlan $plan): View
    {
        abort_unless($plan->is_active, 404);
        $plan->load(['products' => fn ($q) => $q->select('products.id', 'products.name', 'products.slug', 'products.price', 'products.description', 'products.images')]);

        return view('storefront.subscriptions.show', compact('plan'));
    }

    /** Formulario para suscribirse. */
    public function subscribe(SubscriptionPlan $plan): View|RedirectResponse
    {
        abort_unless($plan->is_active, 404);

        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            // Enviamos al login unificado y volvemos aquí después.
            return redirect()->guest(route('login'))
                ->with('info', 'Inicia sesión para comenzar tu ritual mensual.');
        }

        $plan->load('products');
        $addresses = $customer->addresses()->get();
        $default = $customer->defaultAddress();

        return view('storefront.subscriptions.subscribe', compact('plan', 'addresses', 'default', 'customer'));
    }

    /** POST: crea la suscripción y redirige a "Mi cuenta > suscripciones". */
    public function store(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        abort_unless($plan->is_active, 404);

        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'zip_code'       => 'nullable|string|max:10',
            'payment_method' => 'required|in:epayco,cash_on_delivery',
        ]);

        // Evita duplicar una suscripción activa al mismo plan.
        $already = Subscription::where('customer_id', $customer->id)
            ->where('subscription_plan_id', $plan->id)
            ->whereIn('status', ['active', 'paused'])
            ->exists();
        if ($already) {
            return redirect()->route('account.subscriptions')
                ->with('info', 'Ya tienes una suscripción activa a este plan.');
        }

        $subscription = Subscription::create([
            'customer_id'           => $customer->id,
            'subscription_plan_id'  => $plan->id,
            'status'                => 'active',
            'next_delivery_at'      => now()->addDays((int) $plan->interval_days),
            'payment_method'        => $data['payment_method'],
            'shipping_address_json' => [
                'name'     => $data['name'],
                'phone'    => $data['phone'] ?? null,
                'address'  => $data['address'],
                'city'     => $data['city'],
                'state'    => $data['state'],
                'zip_code' => $data['zip_code'] ?? null,
            ],
        ]);

        try {
            Mail::to($customer->email)->send(new SubscriptionCreated($subscription));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('account.subscriptions')
            ->with('success', "🎉 ¡Bienvenida! Tu primer ritual llega el " .
                $subscription->next_delivery_at->translatedFormat('l j \d\e F') . '.');
    }
}
