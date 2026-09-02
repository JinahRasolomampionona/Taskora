/**
 * Sélecteur de rôle du formulaire d'inscription.
 *
 * Met en évidence la carte choisie et n'affiche que les champs pertinents
 * pour ce rôle. Les champs masqués sont désactivés : ils ne sont donc pas
 * envoyés au serveur, qui reste de toute façon la seule autorité sur les
 * données acceptées.
 */
export function initRolePicker() {
    const form = document.querySelector('[data-tk-role-form]');
    if (!form) return;

    const inputs = Array.from(form.querySelectorAll('[data-tk-role-input]'));
    if (!inputs.length) return;

    const groups = Array.from(form.querySelectorAll('[data-tk-role-field]'));

    const apply = () => {
        const selected = inputs.find((input) => input.checked)?.value ?? null;

        inputs.forEach((input) => {
            input.closest('.tk-role-option')?.classList.toggle('is-selected', input.checked);
        });

        groups.forEach((group) => {
            const roles = group.dataset.tkRoleField.split(/\s+/).filter(Boolean);
            const visible = selected !== null && roles.includes(selected);

            group.hidden = !visible;
            group.querySelectorAll('input, select, textarea').forEach((field) => {
                field.disabled = !visible;
            });
        });
    };

    inputs.forEach((input) => input.addEventListener('change', apply));
    apply();
}
