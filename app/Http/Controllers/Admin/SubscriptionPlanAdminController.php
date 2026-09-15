<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubscriptionPlanAdminController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::withCount(['items', 'subscriptions'])
            ->ordered()
            ->paginate(20);

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create(): View
    {
        $plan = new SubscriptionPlan();
        $products = Product::orderBy('name')->get(['id', 'name', 'price']);
        $selected = [];
        return view('admin.subscription-plans.edit', compact('plan', 'products', 'selected'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateForm($request);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('subscription-plans', 'public');
        }

        $plan = SubscriptionPlan::create($data);
        $this->syncItems($plan, $request);

        return redirect()->route('admin.subscription-plans.edit', $plan)
            ->with('success', 'Plan creado.');
    }

    public function edit(SubscriptionPlan $subscription_plan): View
    {
        $plan = $subscription_plan;
        $plan->load('items');
        $products = Product::orderBy('name')->get(['id', 'name', 'price']);
        $selected = $plan->items->map(fn ($it) => [
            'product_id' => $it->product_id,
            'quantity'   => (int) $it->quantity,
            'sort_order' => (int) $it->sort_order,
        ])->values()->all();
        return view('admin.subscription-plans.edit', compact('plan', 'products', 'selected'));
    }

    public function update(Request $request, SubscriptionPlan $subscription_plan): RedirectResponse
    {
        $plan = $subscription_plan;
        $data = $this->validateForm($request);

        if ($data['name'] !== $plan->name || $request->filled('slug_regenerate')) {
            $data['slug'] = $this->uniqueSlug($data['name'], $plan->id);
        }
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($plan->image) Storage::disk('public')->delete($plan->image);
            $data['image'] = $request->file('image')->store('subscription-plans', 'public');
        }
        if ($request->boolean('remove_image') && $plan->image) {
            Storage::disk('public')->delete($plan->image);
            $data['image'] = null;
        }

        $plan->update($data);
        $this->syncItems($plan, $request);

        return redirect()->route('admin.subscription-plans.edit', $plan)
            ->with('success', 'Plan actualizado.');
    }

    public function destroy(SubscriptionPlan $subscription_plan): RedirectResponse
    {
        $plan = $subscription_plan;
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay suscripciones activas para este plan. Desactívalo en su lugar.');
        }
        if ($plan->image) Storage::disk('public')->delete($plan->image);
        $plan->delete();
        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan eliminado.');
    }

    public function toggle(SubscriptionPlan $subscription_plan): RedirectResponse
    {
        $subscription_plan->update(['is_active' => ! $subscription_plan->is_active]);
        return back()->with('success', $subscription_plan->is_active ? 'Plan activado.' : 'Plan desactivado.');
    }

    private function validateForm(Request $request): array
    {
        return $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string|max:2000',
            'image'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'base_price'            => 'required|numeric|min:0',
            'regular_price'         => 'required|numeric|min:0',
            'discount_percent'      => 'nullable|integer|min:0|max:99',
            'interval_days'         => 'required|integer|min:1|max:365',
            'delivery_days_message' => 'nullable|string|max:120',
            'sort_order'            => 'nullable|integer|min:0',
        ]);
    }

    private function syncItems(SubscriptionPlan $plan, Request $request): void
    {
        $ids   = (array) $request->input('items', []);
        $qtys  = (array) $request->input('quantities', []);
        $sorts = (array) $request->input('sort_orders', []);

        // Reseteamos y recreamos: es más simple que sync() por el unique compuesto.
        $plan->items()->delete();
        foreach ($ids as $i => $pid) {
            $pid = (int) $pid;
            if ($pid <= 0) continue;
            $plan->items()->create([
                'product_id' => $pid,
                'quantity'   => max(1, (int) ($qtys[$i] ?? 1)),
                'sort_order' => (int) ($sorts[$i] ?? $i),
            ]);
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base ?: 'plan';
        $i = 2;
        while (SubscriptionPlan::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
