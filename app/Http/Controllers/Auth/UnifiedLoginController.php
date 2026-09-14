<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Login unificado (una sola pantalla en /ingresar).
 *
 * Intenta primero autenticar contra el guard `web` (tabla users) y, si el
 * usuario resulta ser admin, lo lleva al panel. Si el guard web no acepta las
 * credenciales, prueba con el guard `customer` (tabla customers) y lleva al
 * cliente a su área. Si ambos fallan devuelve un error genérico para no
 * revelar qué tabla existe (evita enumeración de cuentas).
 */
class UnifiedLoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión unificado.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa el intento de login para admin o cliente.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        $remember = $request->boolean('remember');

        // 1) Intento como ADMIN (guard `web` + isAdmin()).
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $user = Auth::guard('web')->user();

            if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'));
            }

            // Usuario web que NO es admin: cerramos y seguimos con el guard cliente.
            Auth::guard('web')->logout();
        }

        // 2) Intento como CLIENTE (guard `customer`).
        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Fix UX · si el `intended` apunta a /cuenta/direcciones (típico cuando
            //   el checkout u otro middleware guardó esa URL al pedir auth) el
            //   cliente termina cayendo en "Mis direcciones" en vez del panel.
            //   Consumimos el intended y sólo lo respetamos si NO es esa URL.
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

        // 3) Ambos fallan: mensaje genérico para no filtrar existencia.
        throw ValidationException::withMessages([
            'email' => 'Correo o contraseña incorrectos.',
        ]);
    }

    /**
     * Cierra sesión en AMBOS guards (por si acaso hay una sesión colgada).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada.');
    }
}
