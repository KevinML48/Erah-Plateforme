import Alpine from 'alpinejs';

import './bootstrap';

window.Alpine = Alpine;

Alpine.start();

const initTemplateButtons = () => {
    const buttons = document.querySelectorAll('.tt-btn');

    buttons.forEach((button) => {
        if (button.querySelector(':scope > .tt-btn-inner')) {
            return;
        }

        const inner = document.createElement('span');
        inner.className = 'tt-btn-inner';
        const childNodes = Array.from(button.childNodes);

        childNodes.forEach((node) => {
            if (node.nodeType === Node.TEXT_NODE) {
                const value = node.textContent?.trim();
                if (!value) {
                    return;
                }

                const span = document.createElement('span');
                span.textContent = value;
                span.setAttribute('data-hover', value);
                inner.appendChild(span);
                return;
            }

            if (node.nodeType !== Node.ELEMENT_NODE) {
                return;
            }

            if (!node.classList.contains('tt-btn-icon') && !node.hasAttribute('data-hover')) {
                const label = node.textContent?.trim();
                if (label) {
                    node.setAttribute('data-hover', label);
                }
            }

            inner.appendChild(node);
        });

        if (!inner.childNodes.length) {
            return;
        }

        button.appendChild(inner);
    });
};

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

document.addEventListener('DOMContentLoaded', () => {
    initTemplateButtons();
    initMobileNavigation();
});

const initPwaRegistration = () => {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    window.addEventListener('load', () => {
        navigator.serviceWorker.getRegistrations().then((registrations) => {
            registrations.forEach((registration) => {
                registration.unregister();
            });
        }).catch(() => {
            // Silent fail: cache cleanup must not break the app.
        });

        if ('caches' in window) {
            caches.keys().then((keys) => {
                keys.forEach((key) => {
                    caches.delete(key);
                });
            }).catch(() => {
                // Silent fail: cache cleanup is additive.
            });
        }
    });
};

const disableLegacyPageCache = () => {
    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) {
            return;
        }

        window.location.reload();
    });
};

document.addEventListener('DOMContentLoaded', () => {
    disableLegacyPageCache();
});

initPwaRegistration();
