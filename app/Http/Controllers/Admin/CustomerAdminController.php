<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('orders');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $customers = $query->latest()->paginate(25)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['orders' => function ($q) {
            $q->latest()->take(20);
        }, 'orders.items']);

        return view('admin.customers.show', compact('customer'));
    }
}
