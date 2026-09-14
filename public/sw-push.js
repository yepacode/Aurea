/* Belleza Áurea — Service Worker de notificaciones push (Web Push, VAPID). */

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (event) => event.waitUntil(self.clients.claim()));

self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'Belleza Áurea', body: event.data ? event.data.text() : '' };
    }

    const title = data.title || 'Belleza Áurea';
    const options = {
        body: data.body || '',
        icon: '/img/brand/favicon-192.png',
        badge: '/img/brand/favicon-32.png',
        image: data.image || undefined,
        data: { url: data.url || '/' },
        actions: Array.isArray(data.actions) ? data.actions : [],
        vibrate: [80, 40, 80],
        tag: data.tag || undefined,
        renotify: !!data.renotify,
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((wins) => {
            // Si ya hay una pestaña de la tienda abierta, la enfocamos y navegamos.
            for (const win of wins) {
                if ('focus' in win) {
                    win.focus();
                    if ('navigate' in win) {
                        try { win.navigate(targetUrl); } catch (e) {}
                    }
                    return;
                }
            }
            if (clients.openWindow) return clients.openWindow(targetUrl);
        })
    );
});
