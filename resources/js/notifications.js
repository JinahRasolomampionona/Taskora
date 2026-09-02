/**
 * Notifications UI (toasts).
 *
 * Exposé sur `window.Taskora.notify` pour rester utilisable depuis un
 * attribut Blade ponctuel, tout en restant importable comme module.
 */

const ICONS = {
    success: 'bi-check-circle-fill',
    error: 'bi-exclamation-octagon-fill',
    warning: 'bi-exclamation-triangle-fill',
    info: 'bi-info-circle-fill',
};

let stack = null;

function ensureStack() {
    if (stack && document.body.contains(stack)) {
        return stack;
    }

    stack = document.createElement('div');
    stack.className = 'tk-toast-stack';
    stack.setAttribute('role', 'status');
    stack.setAttribute('aria-live', 'polite');
    document.body.appendChild(stack);

    return stack;
}

export function notify(message, type = 'info', { duration = 4200 } = {}) {
    if (!message) return;

    const toast = document.createElement('div');
    toast.className = `tk-toast tk-toast--${type}`;
    toast.innerHTML = `
        <i class="bi ${ICONS[type] ?? ICONS.info}"></i>
        <div class="flex-grow-1"></div>
        <button type="button" class="tk-toast__close" aria-label="Fermer">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    // Injection en texte : le message peut venir du serveur.
    toast.querySelector('div').textContent = message;

    const dismiss = () => {
        toast.classList.add('is-leaving');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    };

    toast.querySelector('.tk-toast__close').addEventListener('click', dismiss);
    ensureStack().appendChild(toast);

    if (duration > 0) {
        setTimeout(dismiss, duration);
    }

    return toast;
}

/** Rejoue les messages flash rendus par Blade. */
export function flushServerFlashes() {
    document.querySelectorAll('[data-tk-flash]').forEach((node) => {
        notify(node.dataset.tkFlashMessage, node.dataset.tkFlash || 'info');
        node.remove();
    });
}
