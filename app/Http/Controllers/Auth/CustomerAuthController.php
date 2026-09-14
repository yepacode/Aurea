<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showRegister(): View
    {
        return view('account.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'email', 'max:255',
                // Solo bloquea el registro si el correo ya está tomado por una
                // cuenta CON contraseña. Si existe como invitado (sin password)
                // permitimos el "upgrade" y conservamos su historial de pedidos.
                function ($attribute, $value, $fail) {
                    $existing = Customer::where('email', $value)->first();
                    if ($existing && $existing->hasAccount()) {
                        $fail('Ya existe una cuenta con este correo. Inicia sesión.');
                    }
                },
            ],
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'habeas_data' => 'accepted',
        ], [
            'habeas_data.accepted' => 'Debes aceptar la política de tratamiento de datos.',
            'password.confirmed'   => 'Las contraseñas no coinciden.',
            'password.min'         => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Envolvemos en transacción y capturamos UNIQUE violations para cerrar
        // la ventana TOCTOU entre la validación y la escritura (el índice
        // UNIQUE(email) en customers es la garantía final).
        try {
            $customer = DB::transaction(function () use ($data) {
                $existing = Customer::where('email', $data['email'])->lockForUpdate()->first();

                if ($existing && $existing->hasAccount()) {
                    throw ValidationException::withMessages([
                        'email' => 'Ya existe una cuenta con este correo. Inicia sesión.',
                    ]);
                }

                if ($existing) {
                    // Cliente que compró como invitado: se "actualiza" a cuenta
                    // conservando su historial de pedidos.
                    $existing->update([
                        'name'     => $data['name'],
                        'phone'    => $data['phone'] ?? $existing->phone,
                        'password' => $data['password'],
                    ]);

                    return $existing;
                }

                return Customer::create([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'phone'    => $data['phone'] ?? null,
                    'password' => $data['password'],
                ]);
            });
        } catch (QueryException $e) {
            // 23000 = Integrity constraint violation (incluye UNIQUE).
            if ($e->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'email' => 'Ya existe una cuenta con este correo. Inicia sesión.',
                ]);
            }
            throw $e;
        }

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        // Fix UX · si el intended apunta a /cuenta/direcciones (típico cuando
        //   el middleware auth guardó esa URL) no queremos que un registro nuevo
        //   caiga en "Mis direcciones"; mejor al dashboard con el saludo.
        $intended = $request->session()->pull('url.intended');
        if ($intended) {
            $path = parse_url($intended, PHP_URL_PATH) ?: '';
            if (rtrim($path, '/') !== '/cuenta/direcciones') {
                return redirect($intended)
                    ->with('success', '¡Bienvenida a Belleza Áurea! Tu cuenta está lista.');
            }
        }

        return redirect()->route('account.dashboard')
            ->with('success', '¡Bienvenida a Belleza Áurea! Tu cuenta está lista.');
    }

    /**
     * Retro-compat: /cuenta/ingresar sigue existiendo pero redirige al login
     * unificado (una sola pantalla /ingresar detecta admin vs cliente).
     */
    public function showLogin(): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $intended = $request->session()->pull('url.intended');
            if ($intended) {
                $path = parse_url($intended, PHP_URL_PATH) ?: '';
                if (rtrim($path, '/') === '/cuenta/direcciones') {
                    return redirect()->route('account.dashboard');
                }
                return redirect($intended);
            }

            return redirect()->route('account.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Correo o contraseña incorrectos.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada.');
    }
}
