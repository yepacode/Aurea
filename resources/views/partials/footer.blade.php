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
                <a href="{{ locale_route('home') }}" style="display:flex;justify-content:center;max-width:280px;margin-top:-28px;margin-bottom:-14px;">
                    <img src="{{ asset('img/brand/logo-transparent.png') }}" alt="Belleza Áurea" style="height:158px;width:auto;filter:brightness(1.08);">
                </a>
                <p class="mt-5 text-sm" style="color:rgba(247,243,237,0.65);line-height:1.7;max-width:280px;">
                    {{ __('storefront.footer.tagline') }}
                </p>

                {{-- Redes sociales (desde ContactPageSetting en el admin) --}}
                @php($__social = \App\Models\ContactPageSetting::getCurrent())
                @if($__social->instagram_url || $__social->facebook_url || $__social->tiktok_url)
                <div class="mt-6">
                    <p class="text-xs font-semibold uppercase mb-3" style="color:#D9B56D;letter-spacing:0.12em;">{{ __('storefront.footer.follow_us') }}</p>
                    <div class="flex items-center" style="gap:12px;">
                        @if($__social->instagram_url)
                        <a href="{{ $__social->instagram_url }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                           class="flex items-center justify-center transition-colors"
                           style="width:40px;height:40px;border-radius:9999px;border:1px solid rgba(232,204,146,0.3);color:rgba(247,243,237,0.75);"
                           onmouseover="this.style.color='#2E2A26';this.style.background='#D9B56D';this.style.borderColor='#D9B56D'"
                           onmouseout="this.style.color='rgba(247,243,237,0.75)';this.style.background='transparent';this.style.borderColor='rgba(232,204,146,0.3)'">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        @endif
                        @if($__social->facebook_url)
                        <a href="{{ $__social->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
                           class="flex items-center justify-center transition-colors"
                           style="width:40px;height:40px;border-radius:9999px;border:1px solid rgba(232,204,146,0.3);color:rgba(247,243,237,0.75);"
                           onmouseover="this.style.color='#2E2A26';this.style.background='#D9B56D';this.style.borderColor='#D9B56D'"
                           onmouseout="this.style.color='rgba(247,243,237,0.75)';this.style.background='transparent';this.style.borderColor='rgba(232,204,146,0.3)'">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if($__social->tiktok_url)
                        <a href="{{ $__social->tiktok_url }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok"
                           class="flex items-center justify-center transition-colors"
                           style="width:40px;height:40px;border-radius:9999px;border:1px solid rgba(232,204,146,0.3);color:rgba(247,243,237,0.75);"
                           onmouseover="this.style.color='#2E2A26';this.style.background='#D9B56D';this.style.borderColor='#D9B56D'"
                           onmouseout="this.style.color='rgba(247,243,237,0.75)';this.style.background='transparent';this.style.borderColor='rgba(232,204,146,0.3)'">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Tienda --}}
            <div>
                <h4 class="text-sm font-semibold uppercase mb-5" style="color:#D9B56D;letter-spacing:0.12em;font-family:'Playfair Display',serif;">{{ __('storefront.footer.store') }}</h4>
                <ul class="space-y-3">
                    <li><a href="{{ locale_route('products.index') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.footer.all_products') }}</a></li>
                    <li><a href="{{ locale_route('products.index', ['category' => 'rostro-y-piel']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.footer.face_and_skin') }}</a></li>
                    <li><a href="{{ locale_route('products.index', ['category' => 'unas']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.footer.nails') }}</a></li>
                    <li><a href="{{ locale_route('products.index', ['category' => 'pestanas']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.footer.lashes') }}</a></li>
                    <li><a href="{{ locale_route('products.index', ['category' => 'peluqueria']) }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.footer.hair') }}</a></li>
                </ul>
            </div>

            {{-- Información --}}
            <div>
                <h4 class="text-sm font-semibold uppercase mb-5" style="color:#D9B56D;letter-spacing:0.12em;font-family:'Playfair Display',serif;">{{ __('storefront.footer.info') }}</h4>
                <ul class="space-y-3">
                    <li><a href="{{ locale_route('about') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.nav.about') }}</a></li>
                    <li><a href="{{ locale_route('bundles.index') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.nav.kits') }}</a></li>
                    <li><a href="{{ locale_route('subscriptions.index') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.nav.subscriptions') }}</a></li>
                    @if(\Illuminate\Support\Facades\Route::has('blog.index'))
                    <li><a href="{{ route('blog.index') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.nav.blog') }}</a></li>
                    @endif
                    <li><a href="{{ locale_route('shipping-returns') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.nav.shipping') }}</a></li>
                    @if(\Illuminate\Support\Facades\Route::has('faq.index'))
                    <li><a href="{{ route('faq.index') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.nav.faq') }}</a></li>
                    @endif
                    <li><a href="{{ locale_route('contact') }}" class="text-sm transition-colors" style="color:rgba(247,243,237,0.7);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.7)'">{{ __('storefront.nav.contact') }}</a></li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h4 class="text-sm font-semibold uppercase mb-5" style="color:#D9B56D;letter-spacing:0.12em;font-family:'Playfair Display',serif;">{{ __('storefront.footer.newsletter_title') }}</h4>
                <p class="text-sm mb-4" style="color:rgba(247,243,237,0.65);line-height:1.6;">{{ __('storefront.footer.newsletter_copy') }}</p>
                <form action="{{ locale_route('leads.store') }}" method="POST" class="flex">
                    @csrf
                    <input type="hidden" name="source" value="footer">
                    <input type="email" name="email" placeholder="{{ __('storefront.footer.email_placeholder') }}" required
                           aria-label="{{ __('storefront.footer.email_aria') }}"
                           class="flex-1 rounded-l-lg px-4 py-2.5 text-sm focus:outline-none"
                           style="background:rgba(247,243,237,0.08);border:1px solid rgba(232,204,146,0.25);color:#F7F3ED;">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-r-lg text-sm font-semibold transition-colors"
                            style="background:#D9B56D;color:#2E2A26;"
                            onmouseover="this.style.background='#E8CC92'"
                            onmouseout="this.style.background='#D9B56D'">
                        {{ __('storefront.buttons.subscribe') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-14 pt-8" style="border-top:1px solid rgba(232,204,146,0.12);">
            {{-- Enlaces legales --}}
            <div class="flex flex-wrap justify-center items-center mb-6" style="gap:8px 18px;">
                <a href="{{ locale_route('legal.terms') }}" class="text-xs transition-colors" style="color:rgba(247,243,237,0.6);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.6)'">{{ __('storefront.footer.terms') }}</a>
                <span style="color:rgba(232,204,146,0.4);font-size:.7rem;">&#10022;</span>
                <a href="{{ locale_route('legal.privacy') }}" class="text-xs transition-colors" style="color:rgba(247,243,237,0.6);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.6)'">{{ __('storefront.footer.privacy') }}</a>
                <span style="color:rgba(232,204,146,0.4);font-size:.7rem;">&#10022;</span>
                <a href="{{ locale_route('legal.cookies') }}" class="text-xs transition-colors" style="color:rgba(247,243,237,0.6);" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.6)'">{{ __('storefront.footer.cookies') }}</a>
            </div>
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                <p class="text-xs" style="color:rgba(247,243,237,0.45);">{!! __('storefront.footer.rights', ['year' => date('Y')]) !!}</p>
                <p class="text-xs" style="color:rgba(247,243,237,0.45);">{{ __('storefront.footer.tagline_bottom') }}</p>
            </div>
        </div>
    </div>
</footer>
