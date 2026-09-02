/**
 * Ajout d'un commentaire sans rechargement de la page de détail.
 */

import { request } from './http.js';
import { notify } from './notifications.js';

export function initTaskComments() {
    const form = document.querySelector('[data-tk-comment-form]');
    if (!form) return;

    const list = document.querySelector('[data-tk-comment-list]');
    const field = form.querySelector('textarea[name="body"]');
    const submit = form.querySelector('[type="submit"]');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const body = field.value.trim();
        if (body.length < 2) {
            notify('Le commentaire est trop court.', 'warning');
            field.focus();
            return;
        }

        const original = submit.innerHTML;
        submit.setAttribute('disabled', 'disabled');
        submit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Envoi…';

        const response = await request(form.action, {
            method: 'POST',
            body: {
                body,
                is_internal: form.querySelector('[name="is_internal"]')?.checked ?? false,
            },
        });

        submit.removeAttribute('disabled');
        submit.innerHTML = original;

        if (!response.success) {
            notify(response.message, 'error');
            return;
        }

        field.value = '';
        notify(response.message, 'success');
        prepend(list, response.data);
    });
}

function prepend(list, comment) {
    if (!list || !comment) return;

    list.querySelector('[data-tk-comment-empty]')?.remove();

    const node = document.createElement('div');
    node.className = `tk-comment tk-reveal${comment.is_internal ? ' tk-comment--internal' : ''}`;
    node.innerHTML = `
        <span class="tk-avatar" style="background:${comment.author.color}"></span>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-semibold tk-fs-sm" data-name></span>
                <span class="tk-fs-xs tk-text-faint">${comment.created_at}</span>
                ${comment.is_internal ? '<span class="tk-badge tk-badge-neutral">Note interne</span>' : ''}
            </div>
            <div class="tk-comment__body" data-body></div>
        </div>`;

    node.querySelector('.tk-avatar').textContent = comment.author.initials;
    node.querySelector('[data-name]').textContent = comment.author.name;
    node.querySelector('[data-body]').textContent = comment.body;

    list.prepend(node);
}
