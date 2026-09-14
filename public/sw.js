/* Belleza Áurea — Service Worker (network-first, con respaldo offline) */
const CACHE = 'aurea-v1';

// Rutas que NUNCA se cachean (privadas / sensibles / dinámicas).
const NO_CACHE = ['/admin', '/cuenta', '/checkout', '/carrito', '/pedido', '/epayco', '/push'];

self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);
    if (url.origin !== location.origin) return;
    if (NO_CACHE.some((p) => url.pathname.startsWith(p))) return;

    // Network-first: siempre intenta la red; si falla (offline), sirve caché.
    event.respondWith(
        fetch(req)
            .then((res) => {
                if (res && res.status === 200 && res.type === 'basic') {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => {});
                }
                return res;
            })
            .catch(() => caches.match(req))
    );
});
