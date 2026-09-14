<?php

namespace App\Http\Controllers;

use App\Mail\LeadWelcome;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'source' => 'nullable|string|max:50',
        ]);

        $lead = Lead::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? null,
                'source' => $validated['source'] ?? 'footer',
            ],
        );

        if ($lead->wasRecentlyCreated) {
            Mail::to($lead->email)->send(new LeadWelcome($lead));
        }

        $redirect = $request->input('redirect', url()->previous());

        // Cierre de open-redirect: solo permitir rutas del mismo host o
        // relativas (sin host). Cualquier destino externo cae a home.
        $appHost      = parse_url((string) config('app.url'), PHP_URL_HOST);
        $redirectHost = parse_url((string) $redirect, PHP_URL_HOST);
        if ($redirectHost && $redirectHost !== $appHost) {
            $redirect = route('home');
        }

        return redirect($redirect)->with('success', '¡Gracias! Te mantendremos informado.');
    }
}
