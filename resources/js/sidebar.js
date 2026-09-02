/**
 * Sidebar : bascule mobile (offcanvas) et mode réduit sur desktop.
 * La préférence de réduction est conservée d'une page à l'autre.
 */

const STORAGE_KEY = 'taskora.sidebar.collapsed';

export function initSidebar() {
    const shell = document.querySelector('[data-tk-shell]');
    if (!shell) return;

    const backdrop = shell.querySelector('[data-tk-backdrop]');
    const isDesktop = () => window.matchMedia('(min-width: 992px)').matches;

    if (localStorage.getItem(STORAGE_KEY) === '1') {
        shell.classList.add('is-collapsed');
    }

    const closeMobile = () => shell.classList.remove('is-mobile-open');

    shell.querySelectorAll('[data-tk-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            if (isDesktop()) {
                const collapsed = shell.classList.toggle('is-collapsed');
                localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
            } else {
                shell.classList.toggle('is-mobile-open');
            }
        });
    });

    backdrop?.addEventListener('click', closeMobile);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMobile();
    });

    // Le menu mobile ne doit pas rester ouvert après une navigation.
    shell.querySelectorAll('.tk-sidebar a').forEach((link) => {
        link.addEventListener('click', () => {
            if (!isDesktop()) closeMobile();
        });
    });

    window.addEventListener('resize', () => {
        if (isDesktop()) closeMobile();
    });
}
