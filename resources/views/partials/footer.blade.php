<footer style="background:#2E2A26;border-top:1px solid rgba(232,204,146,0.15);position:relative;overflow:hidden;">
    {{-- Resplandor y ramita botánica de marca --}}
    <div style="position:absolute;top:-60px;left:50%;transform:translateX(-50%);width:520px;height:180px;background:radial-gradient(ellipse at 50% 0%, rgba(217,181,109,0.14) 0%, transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;display:flex;justify-content:center;align-items:center;gap:14px;padding-top:26px;">
        <span style="height:1px;width:60px;background:linear-gradient(to right, transparent, rgba(217,181,109,0.5));"></span>
        <svg width="34" height="28" viewBox="0 0 84 67" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:.8;">
            <path d="M42 65C42 45 30 22 6 8" stroke="#D9B56D" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M42 52c-8-2-15-8-18-16M42 40c-9-1-16-6-20-14M42 30c-8 0-15-4-20-11" stroke="#D9B56D" stroke-width="1.3" stroke-linecap="round" opacity=".85"/>
            <path d="M42 65C42 45 54 22 78 8" stroke="#D9B56D" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M42 52c8-2 15-8 18-16M42 40c9-1 16-6 20-14M42 30c8 0 15-4 20-11" stroke="#D9B56D" stroke-width="1.3" stroke-linecap="round" opacity=".85"/>
        </svg>
        <span style="height:1px;width:60px;background:linear-gradient(to left, transparent, rgba(217,181,109,0.5));"></span>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-14" style="position:relative;">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            {{-- Brand --}}
            <div class="md:col-span-1">
                <a href="{{ route('home') }}" style="display:flex;justify-content:center;max-width:280px;margin-top:-28px;margin-bottom:-14px;">
                    <img src="{{ asset('img/brand/logo-transparent.png') }}" alt="Belleza Áurea" style="height:158px;width:auto;filter:brightness(1.08);">
                </a>
                <p class="mt-5 text-sm" style="color:rgba(247,243,237,0.65);line-height:1.7;max-width:280px;">
                    Belleza natural, elegante y atemporal. Skincare, fragancias y rituales premium con ingredientes botánicos.
                </p>
            </div>

            {{-- Tienda --}}
            <div>
                <h4 class="text-sm font-semibold uppercase mb-5" style="color:#D9B56D;letter-spacing:0.12em;font-family:'Playfair Display',serif;">Tienda</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('products.index') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Todos los productos</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'rostro-y-piel']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Rostro y piel</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'unas']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Uñas</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'pestanas']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Pestañas</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'peluqueria']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Peluquería</a></li>
                </ul>
            </div>

            {{-- Información --}}
            <div>
                <h4 class="text-sm font-semibold uppercase mb-5" style="color:#D9B56D;letter-spacing:0.12em;font-family:'Playfair Display',serif;">Información</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Sobre nosotras</a></li>
                    <li><a href="{{ route('landing.quiz') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Quiz de piel</a></li>
                    <li><a href="{{ route('blue-light') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Rituales</a></li>
                    <li><a href="{{ route('shipping-returns') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Envíos y devoluciones</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">Contacto</a></li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h4 class="text-sm font-semibold uppercase mb-5" style="color:#D9B56D;letter-spacing:0.12em;font-family:'Playfair Display',serif;">Mantente al día</h4>
                <p class="text-sm mb-4" style="color:rgba(247,243,237,0.65);line-height:1.6;">Recibe rituales, lanzamientos y un 10% en tu primera compra.</p>
                <form action="{{ route('leads.store') }}" method="POST" class="flex">
                    @csrf
                    <input type="hidden" name="source" value="footer">
                    <input type="email" name="email" placeholder="tu@correo.com" required
                           class="flex-1 rounded-l-lg px-4 py-2.5 text-sm focus:outline-none"
                           style="background:rgba(247,243,237,0.08);border:1px solid rgba(232,204,146,0.25);color:#F7F3ED;">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-r-lg text-sm font-semibold transition-colors"
                            style="background:#D9B56D;color:#2E2A26;"
                            onmouseover="this.style.background='#E8CC92'"
                            onmouseout="this.style.background='#D9B56D'">
                        Suscribirme
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-14 pt-8" style="border-top:1px solid rgba(232,204,146,0.12);">
            {{-- Enlaces legales --}}
            <div class="flex flex-wrap justify-center items-center mb-6" style="gap:8px 18px;">
                <a href="{{ route('legal.terms') }}" class="text-xs transition-colors" style="color:rgba(247,243,237,0.6);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.6)'">Términos y condiciones</a>
                <span style="color:rgba(232,204,146,0.4);font-size:.7rem;">&#10022;</span>
                <a href="{{ route('legal.privacy') }}" class="text-xs transition-colors" style="color:rgba(247,243,237,0.6);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.6)'">Política de privacidad</a>
                <span style="color:rgba(232,204,146,0.4);font-size:.7rem;">&#10022;</span>
                <a href="{{ route('legal.cookies') }}" class="text-xs transition-colors" style="color:rgba(247,243,237,0.6);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.6)'">Política de cookies</a>
            </div>
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                <p class="text-xs" style="color:rgba(247,243,237,0.45);">&copy; {{ date('Y') }} Belleza Áurea. Todos los derechos reservados.</p>
                <p class="text-xs" style="color:rgba(247,243,237,0.45);">Belleza natural · elegante · atemporal</p>
            </div>
        </div>
    </div>
</footer>
