@extends('layouts.app')

@section('title', 'Mis favoritos | Belleza Áurea')
@section('robots', 'noindex, nofollow')

@section('content')
<section style="padding:clamp(32px,5vw,60px) 20px;background:#FBF8F2;min-height:60vh;">
    <div style="max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap;gap:26px;align-items:flex-start;">

        {{-- Sidebar --}}
        <div style="flex:1 1 220px;min-width:220px;max-width:280px;">
            @include('account._nav')
        </div>

        {{-- Main --}}
        <div style="flex:3 1 480px;min-width:0;">

            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3.5vw,32px);font-weight:700;color:#2E2A26;margin:0 0 6px;">Mis favoritos</h1>
            <p style="color:#6B6157;font-size:14px;margin:0 0 22px;">
                <span id="wishlist-count">{{ $products->count() }}</span> producto{{ $products->count() === 1 ? '' : 's' }} guardado{{ $products->count() === 1 ? '' : 's' }}.
            </p>

            @if($products->isEmpty())
                {{-- Estado vacío --}}
                <div style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:clamp(36px,6vw,56px) 24px;text-align:center;box-shadow:0 18px 44px -34px rgba(120,92,44,.45);">
                    <svg style="width:52px;height:52px;color:#E0C88A;margin:0 auto 18px;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <p style="font-family:'Playfair Display',serif;font-size:20px;color:#2E2A26;margin:0 0 8px;">Aún no tienes favoritos</p>
                    <p style="color:#6B6157;font-size:14px;margin:0 0 22px;">Guarda los productos que más te gusten tocando el corazón 🤍</p>
                    <a href="{{ route('products.index') }}"
                       style="display:inline-block;padding:13px 30px;border-radius:9999px;text-decoration:none;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:#3B310F;background:linear-gradient(135deg,#EBCF90,#D9B56D 55%,#C4A057);box-shadow:0 12px 26px -14px rgba(190,154,83,.9);">
                        Explorar catálogo
                    </a>
                </div>
            @else
                <div class="wl-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(14px,2vw,22px);">
                    @foreach($products as $p)
                        @php $img = $p->images[0] ?? null; @endphp
                        <div class="wl-card" data-product-id="{{ $p->id }}"
                             style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 14px 34px -28px rgba(120,92,44,.45);">
                            <a href="{{ route('products.show', $p->slug) }}" style="display:block;text-decoration:none;color:inherit;">
                                <div style="position:relative;aspect-ratio:4/5;overflow:hidden;background:linear-gradient(155deg,#FBF8F2,#F3ECDF);">
                                    @if($img)
                                        <img src="{{ asset('storage/'.$img) }}" alt="{{ $p->name }}" loading="lazy"
                                             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <span style="position:absolute;inset:0;display:grid;place-items:center;text-align:center;padding:14px;font-family:'Playfair Display',serif;font-style:italic;color:#BE9A53;font-size:13px;">{{ $p->name }}</span>
                                    @endif
                                </div>
                            </a>
                            <div style="padding:14px 16px 16px;display:flex;flex-direction:column;flex:1;">
                                @if($p->brand)
                                    <p style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#BE9A53;font-weight:600;margin:0 0 4px;">{{ $p->brand->name }}</p>
                                @endif
                                <a href="{{ route('products.show', $p->slug) }}"
                                   style="font-family:'Playfair Display',serif;font-size:15px;font-weight:600;color:#2E2A26;line-height:1.25;text-decoration:none;margin:0 0 8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    {{ $p->name }}
                                </a>
                                <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:600;color:#BE9A53;margin-bottom:12px;">${{ number_format($p->price, 0, ',', '.') }}</span>

                                <button type="button" onclick="removeFromWishlist({{ $p->id }}, this)"
                                        style="margin-top:auto;width:100%;border:1.5px solid #E5C3BB;background:#fff;color:#C97B6B;
                                               font:600 11.5px/1 'Montserrat',sans-serif;letter-spacing:.06em;text-transform:uppercase;
                                               padding:10px 12px;border-radius:999px;cursor:pointer;transition:all .25s ease;"
                                        onmouseover="this.style.background='rgba(201,123,107,.08)'"
                                        onmouseout="this.style.background='#fff'">
                                    Quitar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</section>
@endsection

@push('scripts')
<style>
    @media(max-width:900px){.wl-grid{grid-template-columns:repeat(2,1fr)!important;}}
    @media(max-width:520px){.wl-grid{grid-template-columns:repeat(2,1fr)!important;gap:12px!important;}}
</style>
<script>
    async function removeFromWishlist(productId, btn) {
        var card = btn.closest('.wl-card');
        try {
            var meta = document.querySelector('meta[name="csrf-token"]');
            var res = await fetch('{{ url('cuenta/favoritos') }}/' + productId, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': meta ? meta.content : '',
                },
            });
            if (!res.ok) throw new Error('bad status');

            if (card) {
                card.style.transition = 'opacity .25s ease, transform .25s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(.96)';
                setTimeout(function () {
                    card.parentNode && card.parentNode.removeChild(card);
                    if (!document.querySelector('.wl-card')) window.location.reload();
                }, 250);
            } else {
                window.location.reload();
            }
        } catch (e) {
            alert('No se pudo quitar de favoritos. Intenta de nuevo.');
        }
    }
</script>
@endpush
