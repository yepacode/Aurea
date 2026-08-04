<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function index(): View
    {
        $customer = $this->customer();
        $addresses = $customer->addresses()->get();

        return view('account.addresses', compact('customer', 'addresses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $customer = $this->customer();

        // Primera dirección = predeterminada automáticamente.
        if ($customer->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        if (! empty($data['is_default'])) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $customer->addresses()->create($data);

        return back()->with('success', 'Dirección guardada.');
    }

    public function update(Request $request, CustomerAddress $address): RedirectResponse
    {
        $this->authorizeAddress($address);
        $data = $this->validateData($request);

        if (! empty($data['is_default'])) {
            $this->customer()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('success', 'Dirección actualizada.');
    }

    public function setDefault(CustomerAddress $address): RedirectResponse
    {
        $this->authorizeAddress($address);
        $this->customer()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Dirección predeterminada actualizada.');
    }

    public function destroy(CustomerAddress $address): RedirectResponse
    {
        $this->authorizeAddress($address);
        $wasDefault = $address->is_default;
        $address->delete();

        // Si borramos la predeterminada, promovemos otra.
        if ($wasDefault) {
            $next = $this->customer()->addresses()->first();
            $next?->update(['is_default' => true]);
        }

        return back()->with('success', 'Dirección eliminada.');
    }

    private function authorizeAddress(CustomerAddress $address): void
    {
        abort_unless($address->customer_id === $this->customer()->id, 403);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'label'     => 'nullable|string|max:60',
            'recipient' => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'required|string|max:500',
            'city'      => 'nullable|string|max:100',
            'state'     => 'required|string|max:100',
            'zip_code'  => 'nullable|string|max:10',
            'is_default'=> 'nullable|boolean',
        ]);
    }
}
