import Alpine from 'alpinejs';

import './bootstrap';

window.Alpine = Alpine;

Alpine.start();

const initMobileNavigation = () => {
    const roots = document.querySelectorAll('[data-mobile-nav-root]');

    roots.forEach((root) => {
        if (root.dataset.mobileNavReady === 'true') {
            return;
        }

        const toggle = root.querySelector('[data-mobile-nav-toggle]');
        const panel = root.querySelector('[data-mobile-nav-panel]');
        const backdrop = root.querySelector('[data-mobile-nav-backdrop]');
        const closeButtons = root.querySelectorAll('[data-mobile-nav-close]');
        const links = panel ? panel.querySelectorAll('[data-mobile-nav-link]') : [];
        const desktopQuery = window.matchMedia('(min-width: 1025px)');
        let closeTimer = null;

        if (!toggle || !panel || !backdrop) {
            return;
        }

        const applyState = (open) => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

            if (closeTimer) {
                window.clearTimeout(closeTimer);
                closeTimer = null;
            }

            if (open) {
                panel.hidden = false;
                backdrop.hidden = false;
                document.body.classList.add('mobile-nav-open');

                window.requestAnimationFrame(() => {
                    panel.classList.add('is-open');
                    backdrop.classList.add('is-open');
                });

                return;
            }

            panel.classList.remove('is-open');
            backdrop.classList.remove('is-open');
            document.body.classList.remove('mobile-nav-open');

            closeTimer = window.setTimeout(() => {
                panel.hidden = true;
                backdrop.hidden = true;
            }, 240);
        };

        const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

        toggle.addEventListener('click', () => {
            applyState(!isOpen());
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => applyState(false));
        });

        backdrop.addEventListener('click', () => applyState(false));

        links.forEach((link) => {
            link.addEventListener('click', () => applyState(false));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && isOpen()) {
                applyState(false);
            }
        });

        if (desktopQuery.addEventListener) {
            desktopQuery.addEventListener('change', (event) => {
                if (event.matches) {
                    applyState(false);
                }
            });
        } else if (desktopQuery.addListener) {
            desktopQuery.addListener((event) => {
                if (event.matches) {
                    applyState(false);
                }
            });
        }

        root.dataset.mobileNavReady = 'true';
    });
};

document.addEventListener('DOMContentLoaded', initMobileNavigation);

const initPwaRegistration = () => {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    const isLocalhost = ['localhost', '127.0.0.1'].includes(window.location.hostname);
    if (window.location.protocol !== 'https:' && !isLocalhost) {
        return;
    }

    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Silent fail: PWA support is additive and must not break the app.
        });
    });
};

initPwaRegistration();
