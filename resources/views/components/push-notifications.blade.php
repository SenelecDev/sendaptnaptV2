{{-- Composant pour activer les notifications push du navigateur --}}
<div x-data="pushNotifications()" x-init="init()" class="hidden">
    {{-- Ce composant s'initialise automatiquement --}}
</div>

@push('scripts')
<script>
function pushNotifications() {
    return {
        supported: false,
        permission: 'default',
        
        async init() {
            // Vérifier le support
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                console.log('Push notifications non supportées');
                return;
            }
            
            this.supported = true;
            this.permission = Notification.permission;
            
            // Enregistrer le service worker
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker enregistré:', registration.scope);
                
                // Demander la permission si pas encore accordée
                if (this.permission === 'default') {
                    await this.requestPermission();
                }
                
                // Écouter les nouvelles notifications internes via polling
                if (this.permission === 'granted') {
                    this.startPolling();
                }
            } catch (error) {
                console.error('Erreur Service Worker:', error);
            }
        },
        
        async requestPermission() {
            try {
                const result = await Notification.requestPermission();
                this.permission = result;
                console.log('Permission notifications:', result);
            } catch (error) {
                console.error('Erreur permission:', error);
            }
        },
        
        startPolling() {
            // Polling toutes les 30 secondes pour les nouvelles notifications
            let lastCheck = Date.now();
            
            setInterval(async () => {
                try {
                    const response = await fetch('/api/notifications/latest?since=' + lastCheck);
                    const data = await response.json();
                    
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notif => {
                            this.showBrowserNotification(notif);
                        });
                    }
                    
                    lastCheck = Date.now();
                } catch (error) {
                    console.error('Erreur polling notifications:', error);
                }
            }, 30000);
        },
        
        showBrowserNotification(notif) {
            if (this.permission !== 'granted') return;
            
            const notification = new Notification(notif.title || 'SENDAPTNAPT', {
                body: notif.message || notif.body || '',
                icon: '/img/logo.png',
                tag: 'notif-' + notif.id,
                requireInteraction: false,
                data: {
                    url: notif.actionUrl || '/notifications'
                }
            });
            
            notification.onclick = function(event) {
                event.preventDefault();
                window.focus();
                if (this.data.url) {
                    window.location.href = this.data.url;
                }
                notification.close();
            };
            
            // Fermer automatiquement après 5 secondes
            setTimeout(() => notification.close(), 5000);
        }
    };
}
</script>
@endpush
