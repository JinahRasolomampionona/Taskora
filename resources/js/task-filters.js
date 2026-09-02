/**
 * Filtrage et recherche des listes de tâches en AJAX.
 *
 * Le formulaire `[data-tk-filters]` pilote un conteneur `[data-tk-results]` :
 * seule la grille est re-rendue, l'URL est synchronisée via l'historique.
 */

import { fetchHtml } from './http.js';
import { notify } from './notifications.js';

export function initTaskFilters() {
    const form = document.querySelector('[data-tk-filters]');
    const results = document.querySelector('[data-tk-results]');

    if (!form || !results) return;

    let controller = null;
    let debounce = null;

    const submit = async () => {
        controller?.abort();
        controller = new AbortController();

        const params = new URLSearchParams(new FormData(form));
        // Une valeur vide ne doit pas polluer l'URL.
        [...params.entries()].forEach(([key, value]) => {
            if (value === '') params.delete(key);
        });

        const query = params.toString();
        const url = `${form.action}${query ? `?${query}` : ''}`;

        results.classList.add('tk-is-loading');
        results.setAttribute('aria-busy', 'true');

        try {
            results.innerHTML = await fetchHtml(url, { signal: controller.signal });
            window.history.replaceState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') {
                notify(error.message || 'Le filtrage a échoué.', 'error');
            }
        } finally {
            results.classList.remove('tk-is-loading');
            results.removeAttribute('aria-busy');
        }
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submit();
    });

    form.querySelectorAll('select, input[type="checkbox"], input[type="date"]').forEach((field) => {
        field.addEventListener('change', submit);
    });

    form.querySelectorAll('input[type="search"], input[data-tk-debounce]').forEach((field) => {
        field.addEventListener('input', () => {
            clearTimeout(debounce);
            debounce = setTimeout(submit, 350);
        });
    });

    // Pagination : la navigation reste interne au conteneur de résultats.
    results.addEventListener('click', async (event) => {
        const link = event.target.closest('.pagination a');
        if (!link) return;

        event.preventDefault();
        results.setAttribute('aria-busy', 'true');

        try {
            results.innerHTML = await fetchHtml(link.href);
            window.history.replaceState({}, '', link.href);
            results.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            notify(error.message || 'Le chargement a échoué.', 'error');
        } finally {
            results.removeAttribute('aria-busy');
        }
    });
}
