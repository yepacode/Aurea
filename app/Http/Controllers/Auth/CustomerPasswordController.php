<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerPasswordController extends Controller
{
    private const BROKER = 'customers';

    public function showForgot(): View
    {
        // Fix prod 500 (feedback 2026-09-22): la vista se pintaba sin `$seoSettings`
        //   mientras que otras vistas de auth ya migraron al pattern estándar. En
        //   producción, si el layout evolucionaba y algún parcial referenciaba
        //   $seoSettings, el render explotaba. Lo pasamos siempre — si la tabla
        //   `seo_settings` aún no existe o la clave no está, cae en null y las
        //   secciones usan su fallback estático.
        $seoSettings = null;
        try {
            $seoSettings = SeoSetting::getForPage('password-forgot');
        } catch (\Throwable $e) {
            // Tabla SEO ausente o clave sin fila — el fallback de la vista aplica.
        }

        return view('account.password-forgot', compact('seoSettings'));
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker(self::BROKER)->sendResetLink(
            $request->only('email')
        );

        // Mensaje neutro (no revelar si el correo existe).
        return back()->with('success', 'Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña.');
    }

    public function showReset(Request $request, string $token): View
    {
        return view('account.password-reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $status = Password::broker(self::BROKER)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->forceFill([
                    'password'       => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($customer));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
