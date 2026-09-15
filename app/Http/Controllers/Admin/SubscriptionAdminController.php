<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionCancelled;
use App\Mail\SubscriptionPaused;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SubscriptionAdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = Subscription::query()
            ->with(['customer', 'plan'])
            ->withCount('deliveries');

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        if ($search = $request->query('search')) {
            $q->whereHas('customer', fn ($qq) => $qq
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $subscriptions = $q->latest()->paginate(20)->withQueryString();

        // KPIs simples para el header.
        $totalActive = Subscription::active()->count();
        $mrr = Subscription::active()
            ->join('subscription_plans', 'subscription_plans.id', '=', 'subscriptions.subscription_plan_id')
            ->sum('subscription_plans.base_price');

        return view('admin.subscriptions.index', compact('subscriptions', 'totalActive', 'mrr'));
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['customer', 'plan.products', 'deliveries.order']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function pause(Request $request, Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->status === 'active', 422);
        $reason = trim((string) $request->input('pause_reason', 'Pausada por administración'));
        $subscription->pause($reason);
        try { Mail::to($subscription->customer->email)->send(new SubscriptionPaused($subscription)); }
        catch (\Throwable $e) { report($e); }
        return back()->with('success', 'Suscripción pausada.');
    }

    public function resume(Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->status === 'paused', 422);
        $subscription->resume();
        return back()->with('success', 'Suscripción reanudada.');
    }

    public function cancel(Subscription $subscription): RedirectResponse
    {
        abort_if($subscription->status === 'cancelled', 422);
        $subscription->cancel();
        try { Mail::to($subscription->customer->email)->send(new SubscriptionCancelled($subscription)); }
        catch (\Throwable $e) { report($e); }
        return back()->with('success', 'Suscripción cancelada.');
    }
}
