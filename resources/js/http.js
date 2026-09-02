/**
 * Client HTTP partagé.
 *
 * Centralise le jeton CSRF, l'en-tête JSON et la normalisation des réponses,
 * pour que chaque module n'ait plus qu'à décrire son action métier.
 */

export function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

/**
 * @returns {Promise<{ok: boolean, status: number, success: boolean, message: string, data: any}>}
 */
export async function request(url, { method = 'GET', body = null, signal = null } = {}) {
    let response;

    try {
        response = await fetch(url, {
            method,
            signal,
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: body ? JSON.stringify(body) : null,
        });
    } catch (error) {
        if (error.name === 'AbortError') {
            throw error;
        }

        return {
            ok: false,
            status: 0,
            success: false,
            message: 'Connexion au serveur impossible. Vérifiez votre réseau.',
            data: null,
        };
    }

    // 419 : la session a expiré, il faut se reconnecter.
    if (response.status === 419) {
        return {
            ok: false,
            status: 419,
            success: false,
            message: 'Votre session a expiré. Rechargez la page pour continuer.',
            data: null,
        };
    }

    let payload = null;
    try {
        payload = await response.json();
    } catch {
        payload = null;
    }

    return {
        ok: response.ok,
        status: response.status,
        success: response.ok && payload?.success !== false,
        message: payload?.message ?? messageForStatus(response.status),
        data: payload?.data ?? null,
        errors: payload?.errors ?? null,
    };
}

/** Récupère un fragment HTML (rafraîchissement partiel des listes). */
export async function fetchHtml(url, { signal = null } = {}) {
    const response = await fetch(url, {
        credentials: 'same-origin',
        signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'text/html',
        },
    });

    if (!response.ok) {
        throw new Error(messageForStatus(response.status));
    }

    return response.text();
}

function messageForStatus(status) {
    switch (status) {
        case 401:
            return 'Vous devez être connecté pour effectuer cette action.';
        case 403:
            return "Vous n'êtes pas autorisé à effectuer cette action.";
        case 404:
            return 'Ressource introuvable.';
        case 422:
            return 'Les données envoyées sont invalides.';
        case 500:
            return 'Une erreur est survenue côté serveur.';
        default:
            return status >= 400 ? 'Une erreur est survenue.' : 'Opération effectuée.';
    }
}
