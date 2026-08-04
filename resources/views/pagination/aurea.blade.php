@php
    $base = 'min-width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;padding:0 12px;border-radius:999px;font-size:14px;font-family:inherit;text-decoration:none;transition:all .2s ease;';
    $link = $base.'color:#6B6157;background:#fff;border:1px solid #E5DCC9;';
    $active = $base.'color:#fff;background:linear-gradient(120deg,#E0BE77,#BE9A53);border:1px solid transparent;font-weight:600;box-shadow:0 8px 16px -8px rgba(190,154,83,.8);';
    $disabled = $base.'color:#C9BEAD;background:#F7F1E6;border:1px solid #EFE7D8;cursor:not-allowed;';
    $dots = 'min-width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;color:#B8A999;';
@endphp

@if ($paginator->hasPages())
<nav role="navigation" aria-label="Paginación" style="display:flex;justify-content:center;align-items:center;gap:6px;margin-top:44px;flex-wrap:wrap;">
    @if ($paginator->onFirstPage())
        <span aria-hidden="true" style="{{ $disabled }}">←</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior" style="{{ $link }}"
           onmouseover="this.style.borderColor='#D9B56D';this.style.color='#BE9A53'" onmouseout="this.style.borderColor='#E5DCC9';this.style.color='#6B6157'">←</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="{{ $dots }}">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" style="{{ $active }}">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="{{ $link }}"
                       onmouseover="this.style.borderColor='#D9B56D';this.style.color='#BE9A53'" onmouseout="this.style.borderColor='#E5DCC9';this.style.color='#6B6157'">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente" style="{{ $link }}"
           onmouseover="this.style.borderColor='#D9B56D';this.style.color='#BE9A53'" onmouseout="this.style.borderColor='#E5DCC9';this.style.color='#6B6157'">→</a>
    @else
        <span aria-hidden="true" style="{{ $disabled }}">→</span>
    @endif
</nav>
@endif
