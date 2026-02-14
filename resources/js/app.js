import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global Alpine data stores
Alpine.store('sidebar', {
    open: true,
    toggle() {
        this.open = !this.open;
    }
});

// Notification helper
Alpine.store('notifications', {
    items: [],
    add(message, type = 'info') {
        const id = Date.now();
        this.items.push({ id, message, type });
        setTimeout(() => this.remove(id), 5000);
    },
    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
    }
});

Alpine.start();
