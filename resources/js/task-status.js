/**
 * Changement de statut sans rechargement.
 *
 * Tout élément portant `data-tk-status` déclenche la transition :
 *   <button data-tk-status="in_progress" data-tk-url="/taches/12/statut" data-tk-task="12">
 *
 * Après succès, les badges et les menus de transition de la page sont
 * rafraîchis à partir de la réponse JSON.
 */

import { request } from './http.js';
import { notify } from './notifications.js';

export function initTaskStatus(root = document) {
    root.addEventListener('click', async (event) => {
        const trigger = event.target.closest('[data-tk-status]');
        if (!trigger) return;

        event.preventDefault();

        if (trigger.dataset.tkConfirm && !window.confirm(trigger.dataset.tkConfirm)) {
            return;
        }

        await applyStatus(trigger);
    });
}

async function applyStatus(trigger) {
    const url = trigger.dataset.tkUrl;
    const status = trigger.dataset.tkStatus;
    const taskId = trigger.dataset.tkTask;

    if (!url || !status) return;

    const restore = setBusy(trigger, taskId);

    const response = await request(url, {
        method: 'PATCH',
        body: { status, comment: trigger.dataset.tkComment || null },
    });

    restore();

    if (!response.success) {
        notify(response.message, 'error');
        return;
    }

    notify(response.message, 'success');
    syncTask(response.data);

    if (trigger.hasAttribute('data-tk-reload')) {
        setTimeout(() => window.location.reload(), 600);
    }
}

/** Met à jour badges, barres de progression et menus liés à la tâche. */
export function syncTask(data) {
    if (!data) return;

    document.querySelectorAll(`[data-tk-status-badge="${data.id}"]`).forEach((badge) => {
        badge.className = `tk-badge ${data.status_class}`;
        badge.textContent = data.status_label;
    });

    document.querySelectorAll(`[data-tk-progress="${data.id}"]`).forEach((bar) => {
        bar.style.width = `${data.progress}%`;
        bar.setAttribute('aria-valuenow', String(data.progress));
    });

    document.querySelectorAll(`[data-tk-transitions="${data.id}"]`).forEach((menu) => {
        renderTransitions(menu, data);
    });

    const card = document.querySelector(`[data-tk-task-card="${data.id}"]`);
    if (card) {
        card.dataset.status = data.status;
    }
}

function renderTransitions(menu, data) {
    const url = menu.dataset.tkUrl;

    if (!data.transitions?.length) {
        menu.innerHTML = '<li><span class="dropdown-item-text tk-fs-xs tk-text-muted">Aucune action disponible</span></li>';
        return;
    }

    menu.innerHTML = data.transitions
        .map(
            (transition) => `
                <li>
                    <button type="button" class="dropdown-item"
                            data-tk-status="${transition.value}"
                            data-tk-url="${url}"
                            data-tk-task="${data.id}">
                        ${escapeHtml(transition.label)}
                    </button>
                </li>`
        )
        .join('');
}

function setBusy(trigger, taskId) {
    const card = taskId ? document.querySelector(`[data-tk-task-card="${taskId}"]`) : null;
    const original = trigger.innerHTML;

    trigger.setAttribute('disabled', 'disabled');
    card?.classList.add('is-busy');

    if (trigger.dataset.tkBusyLabel !== undefined) {
        trigger.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Traitement…';
    }

    return () => {
        trigger.removeAttribute('disabled');
        trigger.innerHTML = original;
        card?.classList.remove('is-busy');
    };
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
}
