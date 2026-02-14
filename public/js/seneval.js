// SenEval - Utilities JavaScript
window.SenEval = window.SenEval || {};

/**
 * Modal management
 */
SenEval.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
};

SenEval.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
};

/**
 * Toast notifications
 */
SenEval.showToast = function(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-y-full`;
    
    const colors = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-600 text-white'
    };
    
    toast.classList.add(...(colors[type] || colors.info).split(' '));
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-full');
    });
    
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

/**
 * Confirm dialog
 */
SenEval.confirm = function(message, callback) {
    if (confirm(message)) {
        callback();
    }
};

/**
 * Format number as currency
 */
SenEval.formatCurrency = function(amount, currency = 'FCFA') {
    return new Intl.NumberFormat('fr-FR').format(amount) + ' ' + currency;
};

/**
 * Format date
 */
SenEval.formatDate = function(date, format = 'short') {
    const options = format === 'long' 
        ? { day: 'numeric', month: 'long', year: 'numeric' }
        : { day: '2-digit', month: '2-digit', year: 'numeric' };
    
    return new Date(date).toLocaleDateString('fr-FR', options);
};

/**
 * Debounce function
 */
SenEval.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

/**
 * Copy to clipboard
 */
SenEval.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        SenEval.showToast('Copié dans le presse-papier', 'success');
    });
};

/**
 * AJAX form submission
 */
SenEval.submitForm = function(formId, callback) {
    const form = document.getElementById(formId);
    if (!form) return;

    const formData = new FormData(form);
    const url = form.action;
    const method = form.method || 'POST';

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (callback) callback(data);
    })
    .catch(error => {
        console.error('Error:', error);
        SenEval.showToast('Une erreur est survenue', 'error');
    });
};

/**
 * Search with debounce
 */
SenEval.initSearch = function(inputId, callback, delay = 300) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const debouncedSearch = SenEval.debounce(callback, delay);
    input.addEventListener('input', () => debouncedSearch(input.value));
};

/**
 * Form validation helpers
 */
SenEval.showFieldError = function(field, message) {
    field.classList.add('border-red-500');
    
    let errorEl = field.parentElement.querySelector('.field-error');
    if (!errorEl) {
        errorEl = document.createElement('p');
        errorEl.className = 'field-error text-sm text-red-600 mt-1';
        field.parentElement.appendChild(errorEl);
    }
    errorEl.textContent = message;
};

SenEval.clearFieldError = function(field) {
    field.classList.remove('border-red-500');
    const errorEl = field.parentElement.querySelector('.field-error');
    if (errorEl) errorEl.remove();
};

SenEval.isValidEmail = function(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
};

/**
 * Auto-dismiss alerts
 */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-auto-dismiss]').forEach(alert => {
        const timeout = parseInt(alert.getAttribute('data-auto-dismiss')) || 5000;
        setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => alert.remove(), 500);
        }, timeout);
    });
});

/**
 * Initialize dropdowns
 */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
        const targetId = button.getAttribute('data-dropdown-toggle');
        const target = document.getElementById(targetId);
        
        if (!target) return;

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            target.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!target.contains(e.target) && !button.contains(e.target)) {
                target.classList.add('hidden');
            }
        });
    });
});

/**
 * Close modals on Escape key
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('[data-modal]:not(.hidden)');
        openModals.forEach(modal => SenEval.closeModal(modal.id));
    }
});
