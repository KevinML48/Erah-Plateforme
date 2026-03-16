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

const initTemplateMainMenuToggle = () => {
    const toggleWrap = document.getElementById('tt-m-menu-toggle-btn-wrap');
    const toggleLink = toggleWrap?.querySelector('.tt-m-menu-toggle-btn');
    const menu = document.querySelector('.tt-main-menu');

    if (!toggleWrap || !toggleLink || !menu) {
        return;
    }

    if (toggleWrap.dataset.menuReady === 'true') {
        return;
    }

    const desktopQuery = window.matchMedia('(min-width: 1025px)');

    const closeSubmenus = () => {
        menu.querySelectorAll('.tt-submenu').forEach((submenu) => {
            submenu.style.display = '';
        });

        menu.querySelectorAll('.tt-submenu-trigger').forEach((trigger) => {
            trigger.classList.remove('tt-m-submenu-open');
        });

        menu.querySelectorAll('.tt-submenu-wrap').forEach((item) => {
            item.classList.remove('tt-submenu-open');
        });
    };

    const applyState = (open) => {
        document.documentElement.classList.toggle('tt-no-scroll', open);
        document.body.classList.toggle('tt-m-menu-open', open);
        document.body.classList.toggle('tt-m-menu-active', open);

        if (!open) {
            closeSubmenus();
        }
    };

    const ensureSubmenuControls = () => {
        menu.querySelectorAll('.tt-submenu-trigger').forEach((trigger) => {
            const submenu = trigger.nextElementSibling;
            if (!submenu?.classList.contains('tt-submenu')) {
                return;
            }

            if (!trigger.querySelector('.tt-submenu-trigger-m')) {
                const overlay = document.createElement('span');
                overlay.className = 'tt-submenu-trigger-m';
                trigger.appendChild(overlay);
            }

            if (!trigger.querySelector('.tt-m-caret')) {
                const caret = document.createElement('span');
                caret.className = 'tt-m-caret';
                trigger.appendChild(caret);
            }
        });
    };

    toggleWrap.addEventListener('click', (event) => {
        if (!window.matchMedia('(max-width: 1024px)').matches) {
            return;
        }

        event.preventDefault();
        ensureSubmenuControls();
        applyState(!document.body.classList.contains('tt-m-menu-open'));
    });

    menu.addEventListener('click', (event) => {
        if (!window.matchMedia('(max-width: 1024px)').matches) {
            return;
        }

        const submenuToggle = event.target instanceof HTMLElement
            ? event.target.closest('.tt-submenu-trigger-m, .tt-m-caret')
            : null;

        if (submenuToggle instanceof HTMLElement) {
            event.preventDefault();

            const trigger = submenuToggle.parentElement;
            const submenu = trigger?.nextElementSibling;
            if (!(trigger instanceof HTMLElement) || !(submenu instanceof HTMLElement)) {
                return;
            }

            const isOpen = trigger.classList.contains('tt-m-submenu-open');

            trigger
                .closest('.tt-submenu-wrap')
                ?.parentElement
                ?.querySelectorAll(':scope > .tt-submenu-wrap > .tt-submenu-trigger.tt-m-submenu-open')
                .forEach((openTrigger) => {
                    if (openTrigger !== trigger) {
                        openTrigger.classList.remove('tt-m-submenu-open');
                        const openSubmenu = openTrigger.nextElementSibling;
                        if (openSubmenu instanceof HTMLElement) {
                            openSubmenu.style.display = 'none';
                        }
                    }
                });

            trigger.classList.toggle('tt-m-submenu-open', !isOpen);
            submenu.style.display = isOpen ? 'none' : 'block';
            return;
        }

        const link = event.target instanceof HTMLElement ? event.target.closest('a[href]') : null;
        if (!(link instanceof HTMLAnchorElement)) {
            return;
        }

        const href = (link.getAttribute('href') || '').trim();
        if (href === '' || href === '#' || href.startsWith('mailto:') || href.startsWith('tel:') || link.target === '_blank') {
            return;
        }

        applyState(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.body.classList.contains('tt-m-menu-open')) {
            applyState(false);
        }
    });

    const handleViewportChange = (event) => {
        if (event.matches) {
            applyState(false);
        }
    };

    if (desktopQuery.addEventListener) {
        desktopQuery.addEventListener('change', handleViewportChange);
    } else if (desktopQuery.addListener) {
        desktopQuery.addListener(handleViewportChange);
    }

    toggleWrap.dataset.menuReady = 'true';
};

document.addEventListener('DOMContentLoaded', () => {
    initTemplateButtons();
    initMobileNavigation();
    initTemplateMainMenuToggle();
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
