<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WholesaleApproved;
use App\Mail\WholesaleRejected;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Panel admin del programa mayorista.
 *
 * Lista los clientes con status pending / approved / rejected (nunca los
 * `none`) para que Karla / el equipo Áurea aprueben o rechacen solicitudes
 * desde /admin/wholesale/requests. Los cambios disparan correos al cliente.
 */
class WholesaleAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()
            ->whereIn('wholesaler_status', ['pending', 'approved', 'rejected']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('wholesaler_company_name', 'like', "%{$q}%")
                    ->orWhere('wholesaler_nit', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('wholesaler_status', $request->status);
        }

        // Pending primero (para que aparezca lo urgente arriba), luego por fecha.
        $customers = $query
            ->orderByRaw("FIELD(wholesaler_status, 'pending', 'rejected', 'approved')")
            ->orderByDesc('wholesaler_requested_at')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => Customer::where('wholesaler_status', 'pending')->count(),
            'approved' => Customer::where('wholesaler_status', 'approved')->count(),
            'rejected' => Customer::where('wholesaler_status', 'rejected')->count(),
        ];

        return view('admin.wholesale.index', compact('customers', 'counts'));
    }

    public function approve(int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        // Debe existir una solicitud previa (no aprobamos de la nada).
        if ($customer->wholesaler_status === 'none') {
            return back()->with('error', 'Esta clienta aún no ha solicitado ser mayorista.');
        }

        $customer->update([
            'is_wholesaler'          => true,
            'wholesaler_status'      => 'approved',
            'wholesaler_approved_at' => now(),
        ]);

        try {
            Mail::to($customer->email)->send(new WholesaleApproved($customer));
        } catch (\Throwable $e) {
            Log::warning('Wholesale approval email failed: '.$e->getMessage());
        }

        return back()->with('success', "Aprobada. Se avisó a {$customer->email}.");
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'notes' => 'required|string|max:1500',
        ], [
            'notes.required' => 'Explica brevemente el motivo (lo verá la clienta).',
        ]);

        $customer->update([
            'is_wholesaler'     => false,
            'wholesaler_status' => 'rejected',
            'wholesaler_notes'  => $data['notes'],
        ]);

        try {
            Mail::to($customer->email)->send(new WholesaleRejected($customer));
        } catch (\Throwable $e) {
            Log::warning('Wholesale rejection email failed: '.$e->getMessage());
        }

        return back()->with('success', "Solicitud rechazada. Se avisó a {$customer->email}.");
    }
}
