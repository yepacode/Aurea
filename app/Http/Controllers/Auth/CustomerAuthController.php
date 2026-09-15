<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CaptureReferral;
use App\Models\Customer;
use App\Models\Referral;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showRegister(Request $request): View
    {
        // Si viene con un código de referida en sesión mostramos "vienes referida por…".
        $referrer = null;
        $refCode = $request->session()->get('referral_code');
        if ($refCode) {
            $referrer = Customer::where('referral_code', $refCode)->first();
        }

        return view('account.register', compact('referrer'));
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

        // Programa "Recomienda y gana": ¿hay un código de referida en sesión?
        $referralCode  = $request->session()->get('referral_code');
        $referralSrc   = $request->session()->get('referral_source');
        $referrer      = $referralCode ? Customer::where('referral_code', $referralCode)->first() : null;

        // Envolvemos en transacción y capturamos UNIQUE violations para cerrar
        // la ventana TOCTOU entre la validación y la escritura (el índice
        // UNIQUE(email) en customers es la garantía final).
        try {
            $customer = DB::transaction(function () use ($data, $referrer, $referralSrc) {
                $existing = Customer::where('email', $data['email'])->lockForUpdate()->first();

                if ($existing && $existing->hasAccount()) {
                    throw ValidationException::withMessages([
                        'email' => 'Ya existe una cuenta con este correo. Inicia sesión.',
                    ]);
                }

                if ($existing) {
                    // Cliente que compró como invitado: se "actualiza" a cuenta
                    // conservando su historial de pedidos.
                    $existing->fill([
                        'name'            => $data['name'],
                        'phone'           => $data['phone'] ?? $existing->phone,
                        'password'        => $data['password'],
                    ]);

                    // referred_by_customer_id NO es fillable (privilegio). Asignación
                    // explícita solo si aún no tiene referrer y no es un self-referral.
                    if ($referrer && ! $existing->referred_by_customer_id && $referrer->id !== $existing->id) {
                        $existing->referred_by_customer_id = $referrer->id;
                        $existing->referral_source         = $referralSrc;
                    }

                    $existing->save();

                    return $existing;
                }

                $newCustomer = new Customer();
                $newCustomer->fill([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'phone'    => $data['phone'] ?? null,
                    'password' => $data['password'],
                    'referral_source' => $referrer ? $referralSrc : null,
                ]);
                // referred_by_customer_id NO es fillable (privilegio) → explícito.
                $newCustomer->referred_by_customer_id = $referrer?->id;
                $newCustomer->save();

                return $newCustomer;
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

        // Registrar la referida en pending (idempotente por UNIQUE(referred_customer_id)).
        if ($customer->referred_by_customer_id && $customer->referred_by_customer_id !== $customer->id) {
            Referral::firstOrCreate(
                ['referred_customer_id' => $customer->id],
                [
                    'referrer_customer_id' => $customer->referred_by_customer_id,
                    'status'               => 'pending',
                ],
            );
        }

        // Ya consumimos la referida: limpiamos sesión y cookies para no re-aplicar.
        $request->session()->forget(['referral_code', 'referral_source']);
        Cookie::queue(Cookie::forget(CaptureReferral::COOKIE_KEY));
        Cookie::queue(Cookie::forget(CaptureReferral::COOKIE_SOURCE_KEY));

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
