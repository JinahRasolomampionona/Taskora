/**
 * Assignation d'une tâche à un agent, en AJAX.
 *
 * Utilisé depuis le détail d'une tâche et depuis les listes administrateur.
 */

import { request } from './http.js';
import { notify } from './notifications.js';
import { syncTask } from './task-status.js';

export function initTaskAssign() {
    document.addEventListener('click', async (event) => {
        const trigger = event.target.closest('[data-tk-assign]');
        if (!trigger) return;

        event.preventDefault();

        const url = trigger.dataset.tkUrl;
        if (!url) return;

        const agentId = trigger.dataset.tkAssign === '' ? null : Number(trigger.dataset.tkAssign);

        trigger.setAttribute('disabled', 'disabled');

        const response = await request(url, {
            method: 'PATCH',
            body: { assigned_to: agentId },
        });

        trigger.removeAttribute('disabled');

        if (!response.success) {
            notify(response.message, 'error');
            return;
        }

        notify(response.message, 'success');
        syncTask(response.data);
        renderAgent(response.data);
    });
}

function renderAgent(data) {
    if (!data) return;

    document.querySelectorAll(`[data-tk-agent-slot="${data.id}"]`).forEach((slot) => {
        if (!data.agent) {
            slot.innerHTML = '<span class="tk-fs-sm tk-text-faint">Non assignée</span>';
            return;
        }

        slot.innerHTML = `
            <span class="tk-user-chip">
                <span class="tk-avatar" style="background:${data.agent.color}">${escapeHtml(data.agent.initials)}</span>
                <span class="tk-user-chip__meta">
                    <span class="tk-user-chip__name"></span>
                </span>
            </span>`;
        slot.querySelector('.tk-user-chip__name').textContent = data.agent.name;
    });
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
}
