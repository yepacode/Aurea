<?php

namespace App\Http\Controllers;

use App\Models\NpsResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NpsController extends Controller
{
    /**
     * Muestra el formulario NPS.
     * Si $score viene en la URL (el clic en el email lo trae precargado),
     * lo dejamos seleccionado.
     */
    public function respond(string $token, ?int $score = null): View|RedirectResponse
    {
        $nps = NpsResponse::where('token', $token)->firstOrFail();

        // Si ya respondió, mándalo directo al agradecimiento.
        if ($nps->responded_at) {
            return redirect()->route('nps.thanks', ['token' => $token]);
        }

        // Precarga el score si viene por URL y es válido.
        $preselect = ($score !== null && $score >= 0 && $score <= 10) ? $score : null;

        return view('nps.form', [
            'nps'       => $nps,
            'preselect' => $preselect,
        ]);
    }

    public function submit(Request $request, string $token): RedirectResponse
    {
        $nps = NpsResponse::where('token', $token)->firstOrFail();

        if ($nps->responded_at) {
            return redirect()->route('nps.thanks', ['token' => $token]);
        }

        $data = $request->validate([
            'score'   => ['required', 'integer', 'between:0,10'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $nps->update([
            'score'        => $data['score'],
            'comment'      => $data['comment'] ?? null,
            'responded_at' => now(),
        ]);

        return redirect()->route('nps.thanks', ['token' => $token]);
    }

    public function thanks(string $token): View
    {
        $nps = NpsResponse::where('token', $token)->firstOrFail();

        return view('nps.thanks', ['nps' => $nps]);
    }
}
