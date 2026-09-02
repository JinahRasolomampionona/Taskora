/**
 * Kanban : déplacement des cartes entre colonnes.
 *
 * Le drag & drop n'est qu'un raccourci d'interface : la transition est
 * toujours validée par le serveur (TaskStatus + TaskPolicy). En cas de refus,
 * la carte revient visuellement à sa position d'origine.
 */

import Sortable from 'sortablejs';
import { request } from './http.js';
import { notify } from './notifications.js';
import { syncTask } from './task-status.js';

export function initTaskBoard() {
    const board = document.querySelector('[data-tk-board]');
    if (!board) return;

    const lists = board.querySelectorAll('[data-tk-board-list]');
    const draggable = board.dataset.tkBoard === 'draggable';

    lists.forEach((list) => {
        Sortable.create(list, {
            group: 'taskora-board',
            animation: 160,
            disabled: !draggable,
            ghostClass: 'tk-drag-ghost',
            chosenClass: 'tk-drag-chosen',
            dragClass: 'tk-drag-active',
            forceFallback: true,
            fallbackOnBody: true,
            filter: '.tk-board__empty',
            onStart: () => lists.forEach((l) => l.classList.add('tk-drop-active')),
            onEnd: async (event) => {
                lists.forEach((l) => l.classList.remove('tk-drop-active'));
                await handleDrop(event, board);
                refreshCounts(board);
            },
        });
    });

    refreshCounts(board);
}

async function handleDrop(event, board) {
    const { item, from, to, oldIndex } = event;

    if (from === to) return;

    const status = to.dataset.tkBoardList;
    const url = item.dataset.tkUrl;
    const taskId = item.dataset.tkTaskCard;

    const revert = () => {
        from.insertBefore(item, from.children[oldIndex] ?? null);
        refreshCounts(board);
    };

    if (!url || !status) {
        revert();
        return;
    }

    item.classList.add('is-busy');

    const response = await request(url, { method: 'PATCH', body: { status } });

    item.classList.remove('is-busy');

    if (!response.success) {
        notify(response.message, 'error');
        revert();
        return;
    }

    notify(response.message, 'success');
    syncTask(response.data);
    toggleEmptyStates(board);
}

/** Recalcule les compteurs de chaque colonne. */
function refreshCounts(board) {
    board.querySelectorAll('[data-tk-board-column]').forEach((column) => {
        const list = column.querySelector('[data-tk-board-list]');
        const count = list ? list.querySelectorAll('[data-tk-task-card]').length : 0;
        const badge = column.querySelector('[data-tk-board-count]');

        if (badge) badge.textContent = String(count);
    });

    toggleEmptyStates(board);
}

/** Affiche ou masque le placeholder « aucune tâche » de chaque colonne. */
function toggleEmptyStates(board) {
    board.querySelectorAll('[data-tk-board-list]').forEach((list) => {
        const placeholder = list.querySelector('.tk-board__empty');
        if (!placeholder) return;

        const hasCards = list.querySelectorAll('[data-tk-task-card]').length > 0;
        placeholder.classList.toggle('d-none', hasCards);
    });
}
