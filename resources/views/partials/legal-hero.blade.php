{{--
    Encabezado reutilizable para páginas legales.
    Params: $title (string), $subtitle (string|null), $current (string, miga de pan)
--}}
<section class="relative overflow-hidden" style="background:#2E2A26;">
    <div class="absolute inset-0" style="opacity:.32;background:radial-gradient(ellipse at 50% 25%, #D9B56D 0%, transparent 62%);"></div>

    {{-- Ramitas doradas en las esquinas --}}
    <svg class="absolute" style="top:26px;left:26px;width:70px;height:56px;opacity:.5;" viewBox="0 0 84 67" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M42 65C42 45 30 22 6 8" stroke="#D9B56D" stroke-width="1.4" stroke-linecap="round"/>
        <path d="M42 52c-8-2-15-8-18-16M42 40c-9-1-16-6-20-14M42 30c-8 0-15-4-20-11" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round" opacity=".85"/>
    </svg>
    <svg class="absolute" style="bottom:20px;right:26px;width:70px;height:56px;opacity:.5;transform:scaleX(-1);" viewBox="0 0 84 67" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M42 65C42 45 30 22 6 8" stroke="#D9B56D" stroke-width="1.4" stroke-linecap="round"/>
        <path d="M42 52c-8-2-15-8-18-16M42 40c-9-1-16-6-20-14M42 30c-8 0-15-4-20-11" stroke="#D9B56D" stroke-width="1.2" stroke-linecap="round" opacity=".85"/>
    </svg>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24 text-center">
        <nav class="mb-7 text-sm" style="color:rgba(247,243,237,0.55);">
            <a href="{{ route('home') }}" class="transition-colors" onmouseover="this.style.color='#D9B56D'" onmouseout="this.style.color='rgba(247,243,237,0.55)'">Inicio</a>
            <span class="mx-2">/</span>
            <span style="color:rgba(247,243,237,0.9);">{{ $current ?? $title }}</span>
        </nav>

        <h1 class="font-brand text-4xl md:text-5xl font-bold" style="color:#FBF8F2;">{{ $title }}</h1>

        @if(!empty($subtitle))
            <p class="mt-5 text-lg max-w-2xl mx-auto" style="color:rgba(247,243,237,0.78);">{{ $subtitle }}</p>
        @endif

        <div style="position:relative;width:120px;height:20px;margin:26px auto 0;">
            <span class="ba-dew" style="left:12px;top:6px;--d:0s;"></span>
            <span style="position:absolute;left:32px;top:9px;width:56px;height:2px;background:#D9B56D;border-radius:2px;box-shadow:0 0 12px rgba(217,181,109,0.65);"></span>
            <span class="ba-dew" style="right:12px;top:6px;--d:.8s;"></span>
        </div>
    </div>
</section>
