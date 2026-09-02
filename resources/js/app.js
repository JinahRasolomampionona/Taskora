/**
 * Point d'entrée JavaScript de Taskora.
 *
 * Chaque comportement vit dans son propre module ; ce fichier se contente
 * de les câbler une fois le DOM prêt.
 */

import * as bootstrap from 'bootstrap';

import { initSidebar } from './sidebar.js';
import { initRolePicker } from './auth-role.js';
import { initPasswordToggles } from './password-toggle.js';
import { initTaskStatus } from './task-status.js';
import { initTaskFilters } from './task-filters.js';
import { initTaskBoard } from './task-board.js';
import { initTaskAssign } from './task-assign.js';
import { initTaskComments } from './task-comments.js';
import { notify, flushServerFlashes } from './notifications.js';

window.bootstrap = bootstrap;
window.Taskora = { notify };

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initRolePicker();
    initPasswordToggles();
    initTaskStatus();
    initTaskFilters();
    initTaskBoard();
    initTaskAssign();
    initTaskComments();
    flushServerFlashes();

    enableTooltips();
    enableConfirmations();
    enableCopyButtons();
});

/** Infobulles Bootstrap sur les éléments annotés. */
function enableTooltips() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
        bootstrap.Tooltip.getOrCreateInstance(el);
    });
}

/** Confirmation avant les actions destructrices (suppression, annulation). */
function enableConfirmations() {
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-tk-confirm]');
        if (!form) return;

        if (!window.confirm(form.dataset.tkConfirm)) {
            event.preventDefault();
        }
    });
}

/** Boutons « remplir » des comptes de démonstration sur l'écran de connexion. */
function enableCopyButtons() {
    document.querySelectorAll('[data-tk-fill-login]').forEach((button) => {
        button.addEventListener('click', () => {
            const form = document.querySelector('[data-tk-login-form]');
            if (!form) return;

            form.querySelector('[name="email"]').value = button.dataset.tkFillLogin;
            form.querySelector('[name="password"]').value = button.dataset.tkFillPassword;
            form.querySelector('[name="email"]').focus();
        });
    });
}
