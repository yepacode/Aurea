<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login | Belleza Áurea</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree:wght@400;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-bg text-text font-body min-h-screen flex items-center justify-center antialiased">
    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <img src="{{ asset('img/brand/logo-transparent.png') }}" alt="Belleza Áurea" class="object-contain mx-auto mb-3" style="height:144px;max-width:200px;width:auto;">
            <div>
                <span style="font-family:'Playfair Display',serif;font-size:30px;font-weight:600;color:#2E2A26;letter-spacing:0.02em;">Belleza</span>
                <span style="font-family:'Playfair Display',serif;font-size:30px;font-weight:600;color:#D9B56D;font-style:italic;margin-left:6px;">Áurea</span>
            </div>
            <p class="mt-2 text-sm" style="color:#6B6157;">Panel de administración</p>
        </div>

        <div class="bg-surface border border-border rounded-2xl p-8">
            <h1 class="font-brand text-xl font-semibold text-center">Iniciar sesión</h1>

            @if($errors->any())
                <div class="mt-4 bg-danger/10 border border-danger/30 text-danger px-4 py-3 rounded-lg text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium mb-1" style="color:#4B4541;">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full border border-border rounded-lg px-4 py-2.5 placeholder-muted/30 focus:outline-none focus:border-secondary transition-colors" style="color:#2E2A26;background:#FFFFFF;">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1" style="color:#4B4541;">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           class="w-full border border-border rounded-lg px-4 py-2.5 placeholder-muted/30 focus:outline-none focus:border-secondary transition-colors" style="color:#2E2A26;background:#FFFFFF;">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-border bg-bg text-secondary focus:ring-secondary">
                    <label for="remember" class="ml-2 text-sm" style="color:#4B4541;">Recordarme</label>
                </div>

                <button type="submit"
                        class="w-full bg-secondary hover:bg-secondary/90 text-white py-2.5 rounded-lg font-medium transition-colors">
                    Entrar
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-muted/30">&copy; {{ date('Y') }} Belleza Áurea</p>
    </div>
</body>
</html>
