{{-- ============================================================
     WISHLIST — función global toggleWishlist (JS puro, sin Alpine)
     ============================================================ --}}
<script>
    window.__customerLoggedIn = {{ auth('customer')->check() ? 'true' : 'false' }};
    window.__loginUrl = "{{ route('customer.login') }}";
    window.__wishlistToggleUrl = "{{ route('wishlist.toggle') }}";

    (function () {
        function baWishToast(msg) {
            try {
                var t = document.createElement('div');
                t.textContent = msg;
                t.style.cssText = 'position:fixed;bottom:26px;left:50%;transform:translateX(-50%);' +
                    'z-index:9998;background:#2E2A26;color:#fff;padding:13px 22px;border-radius:999px;' +
                    "font-size:13.5px;font-weight:500;font-family:'Montserrat',sans-serif;" +
                    'box-shadow:0 16px 32px -12px rgba(0,0,0,.4);opacity:0;transition:opacity .25s ease;';
                document.body.appendChild(t);
                requestAnimationFrame(function () { t.style.opacity = '1'; });
                setTimeout(function () {
                    t.style.opacity = '0';
                    setTimeout(function () { t.parentNode && t.parentNode.removeChild(t); }, 300);
                }, 2200);
            } catch (e) { /* noop */ }
        }

        window.toggleWishlist = async function (productId, el, event) {
            if (event) { event.preventDefault(); event.stopPropagation(); }

            if (!window.__customerLoggedIn) {
                window.location = window.__loginUrl;
                return;
            }

            try {
                var meta = document.querySelector('meta[name="csrf-token"]');
                var res = await fetch(window.__wishlistToggleUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': meta ? meta.content : '',
                    },
                    body: JSON.stringify({ product_id: productId }),
                });

                if (!res.ok) throw new Error('bad status');
                var data = await res.json();

                if (el) {
                    var svg = el.querySelector('svg');
                    if (data.in_wishlist) {
                        el.classList.add('is-wished');
                        if (svg) svg.setAttribute('fill', '#C97B6B');
                    } else {
                        el.classList.remove('is-wished');
                        if (svg) svg.setAttribute('fill', 'none');
                    }
                }

                var counter = document.getElementById('wishlist-count');
                if (counter && typeof data.count !== 'undefined') {
                    counter.textContent = data.count;
                }

                baWishToast(data.message || (data.in_wishlist ? 'Agregado a favoritos' : 'Quitado de favoritos'));
            } catch (e) {
                baWishToast('No se pudo actualizar');
            }
        };
    })();
</script>
