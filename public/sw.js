// Service Worker pour SENDAPTNAPT
const CACHE_NAME = 'sendaptnapt-v1';

// Installation du service worker
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installation...');
    self.skipWaiting();
});

// Activation
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activation...');
    event.waitUntil(clients.claim());
});

// Écoute des notifications push
self.addEventListener('push', (event) => {
    console.log('Service Worker: Push reçu');
    
    let data = {
        title: 'SENDAPTNAPT',
        body: 'Nouvelle notification',
        icon: '/img/logo.png',
        badge: '/img/logo.png',
        tag: 'notification-' + Date.now(),
        requireInteraction: false,
        data: {}
    };
    
    if (event.data) {
        try {
            const payload = event.data.json();
            data = { ...data, ...payload };
        } catch (e) {
            data.body = event.data.text();
        }
    }
    
    const options = {
        body: data.body,
        icon: data.icon || '/img/logo.png',
        badge: data.badge || '/img/logo.png',
        tag: data.tag,
        requireInteraction: data.requireInteraction || false,
        data: data.data || {},
        vibrate: [100, 50, 100],
        actions: data.actions || [
            { action: 'view', title: 'Voir' },
            { action: 'dismiss', title: 'Fermer' }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Click sur notification
self.addEventListener('notificationclick', (event) => {
    console.log('Service Worker: Click sur notification');
    event.notification.close();
    
    const url = event.notification.data?.url || '/dashboard';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Chercher une fenêtre existante
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.focus();
                    client.navigate(url);
                    return;
                }
            }
            // Ouvrir une nouvelle fenêtre si aucune n'existe
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

// Fermeture notification
self.addEventListener('notificationclose', (event) => {
    console.log('Service Worker: Notification fermée');
});
