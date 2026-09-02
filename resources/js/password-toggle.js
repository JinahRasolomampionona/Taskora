/**
 * Bouton œil des champs mot de passe (composant <x-password-field>).
 *
 * Le bouton est rendu masqué côté serveur : il n'apparaît que si ce module
 * s'exécute, pour ne jamais afficher une commande inopérante.
 */
export function initPasswordToggles() {
    document.querySelectorAll('[data-tk-password-toggle]').forEach((button) => {
        const input = document.getElementById(button.getAttribute('aria-controls'));
        if (!input) return;

        button.hidden = false;
        button.addEventListener('click', () => toggle(input, button));
    });
}

function toggle(input, button) {
    const wasVisible = input.type === 'text';
    const label = wasVisible ? 'Afficher le mot de passe' : 'Masquer le mot de passe';

    input.type = wasVisible ? 'password' : 'text';

    button.setAttribute('aria-pressed', String(!wasVisible));
    button.setAttribute('aria-label', label);
    button.setAttribute('title', label);
    button.querySelector('i').className = wasVisible ? 'bi bi-eye' : 'bi bi-eye-slash';

    // Rendre la main au champ, curseur en fin de saisie.
    const value = input.value;
    input.focus({ preventScroll: true });
    input.setSelectionRange?.(value.length, value.length);
}
