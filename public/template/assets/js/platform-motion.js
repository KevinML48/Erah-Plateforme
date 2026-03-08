(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    }

    function uniqueElements(selectors) {
        var seen = new Set();
        var nodes = [];

        selectors.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (element) {
                if (!seen.has(element)) {
                    seen.add(element);
                    nodes.push(element);
                }
            });
        });

        return nodes;
    }

    function normalizeText(value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    }

    function addClassToElements(selectors, className) {
        uniqueElements(selectors).forEach(function (element) {
            element.classList.add(className);
        });
    }

    function hydrateThemeButtons() {
        document.querySelectorAll('.tt-btn .tt-btn-inner').forEach(function (inner) {
            if (inner.dataset.platformMotionNormalized === '1') {
                return;
            }

            inner.dataset.platformMotionNormalized = '1';

            Array.prototype.slice.call(inner.childNodes).forEach(function (node) {
                if (node.nodeType !== 3) {
                    return;
                }

                var label = normalizeText(node.textContent);
                if (!label) {
                    node.remove();
                    return;
                }

                var span = document.createElement('span');
                span.textContent = label;
                span.setAttribute('data-hover', label);
                inner.replaceChild(span, node);
            });

            var hasContentChild = false;

            Array.prototype.slice.call(inner.children).forEach(function (child) {
                if (child.classList.contains('tt-btn-icon')) {
                    return;
                }

                hasContentChild = true;

                var label = normalizeText(child.getAttribute('data-hover')) || normalizeText(child.textContent);
                if (label) {
                    child.setAttribute('data-hover', label);
                }
            });

            if (!hasContentChild) {
                var fallbackLabel = normalizeText(inner.textContent);
                if (!fallbackLabel) {
                    return;
                }

                inner.textContent = '';

                var fallback = document.createElement('span');
                fallback.textContent = fallbackLabel;
                fallback.setAttribute('data-hover', fallbackLabel);
                inner.appendChild(fallback);
            }
        });
    }

    function assignMotionClasses() {
        addClassToElements([
            '.adm-surface',
            '.home-summary-card',
            '.profile-form-card',
            '.profile-side-card',
            '.profile-kpi-card',
            '.profile-shortcut-card',
            '.tx-filters-card',
            '.tx-history-card',
            '.ui-card',
            '.app-card',
            '.section',
            '.table-wrap',
            '.dev-card',
            '.dev-mini-card',
            '.adm-supporter-card',
            '.supporter-card'
        ], 'platform-motion-surface');

        addClassToElements([
            '.actions .button-link',
            '.actions button',
            '.button-link',
            '.btn',
            '.app-dropdown summary',
            '.adm-gallery-btn',
            '.adm-nav .tt-btn',
            '.adm-row-actions .tt-btn',
            '.adm-filter-actions .tt-btn'
        ], 'platform-motion-button');

        addClassToElements([
            '.nav-links a'
        ], 'platform-motion-nav-link');
    }

    function bindPointerGlow() {
        if (!window.matchMedia || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            return;
        }

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        uniqueElements([
            '.platform-motion-surface',
            '.platform-motion-button:not(.tt-btn)',
            '.platform-motion-nav-link'
        ]).forEach(function (element) {
            if (element.dataset.platformMotionPointer === '1') {
                return;
            }

            element.dataset.platformMotionPointer = '1';

            element.addEventListener('pointermove', function (event) {
                var rect = element.getBoundingClientRect();
                if (!rect.width || !rect.height) {
                    return;
                }

                var x = ((event.clientX - rect.left) / rect.width) * 100;
                var y = ((event.clientY - rect.top) / rect.height) * 100;

                element.style.setProperty('--motion-x', x.toFixed(2) + '%');
                element.style.setProperty('--motion-y', y.toFixed(2) + '%');
            });

            element.addEventListener('pointerleave', function () {
                element.style.removeProperty('--motion-x');
                element.style.removeProperty('--motion-y');
            });
        });
    }

    function introTargets() {
        return uniqueElements([
            '.header-row .brand',
            '.header-row .nav-links > *',
            '.ph-social',
            '.tt-scroll-down',
            '.adm-nav',
            '.app-toast-stack .app-toast',
            '.tt-toast-stack .tt-toast'
        ]).filter(function (element) {
            return element.offsetParent !== null;
        });
    }

    function revealTargets() {
        return uniqueElements([
            '.adm-surface',
            '.tt-grid-items-wrap > .tt-grid-item',
            '.home-summary-card:not(.tt-anim-fadeinup)',
            '.profile-form-card',
            '.profile-side-card',
            '.profile-kpi-card',
            '.profile-shortcut-card',
            '.tx-filters-card',
            '.tx-history-card',
            '.ui-card',
            '.app-card',
            '.section',
            '.table-wrap',
            '.dev-card',
            '.dev-mini-card',
            '.adm-supporter-card:not(.tt-anim-fadeinup)',
            '.supporter-card:not(.tt-anim-fadeinup)'
        ]).filter(function (element) {
            if (element.dataset.platformMotionReveal === '1') {
                return false;
            }

            if (element.closest('#tt-page-transition') || element.closest('#tt-header')) {
                return false;
            }

            return element.offsetParent !== null;
        });
    }

    function animateIntro() {
        var targets = introTargets();
        if (!targets.length) {
            return;
        }

        if (window.gsap) {
            window.gsap.fromTo(
                targets,
                { autoAlpha: 0, y: 18 },
                {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.06,
                    ease: 'power3.out',
                    clearProps: 'all'
                }
            );

            return;
        }

        targets.forEach(function (element, index) {
            element.animate(
                [
                    { opacity: 0, transform: 'translate3d(0, 18px, 0)' },
                    { opacity: 1, transform: 'translate3d(0, 0, 0)' }
                ],
                {
                    duration: 650,
                    delay: index * 70,
                    easing: 'cubic-bezier(.22,1,.36,1)',
                    fill: 'both'
                }
            );
        });
    }

    function animateWithGsap(targets) {
        if (!window.gsap || !window.ScrollTrigger || !targets.length) {
            return false;
        }

        if (typeof window.gsap.registerPlugin === 'function') {
            window.gsap.registerPlugin(window.ScrollTrigger);
        }

        targets.forEach(function (element) {
            element.dataset.platformMotionReveal = '1';
        });

        window.ScrollTrigger.batch(targets, {
            start: 'top 88%',
            once: true,
            onEnter: function (batch) {
                window.gsap.fromTo(
                    batch,
                    {
                        autoAlpha: 0,
                        y: 52,
                        scale: 0.985,
                        rotateX: 4,
                        transformOrigin: '50% 100%'
                    },
                    {
                        autoAlpha: 1,
                        y: 0,
                        scale: 1,
                        rotateX: 0,
                        duration: 1.05,
                        stagger: 0.1,
                        ease: 'power3.out',
                        clearProps: 'all'
                    }
                );
            }
        });

        window.setTimeout(function () {
            window.ScrollTrigger.refresh();
        }, 80);

        return true;
    }

    function animateWithObserver(targets) {
        if (!('IntersectionObserver' in window) || !targets.length) {
            return false;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                var element = entry.target;
                var index = parseInt(element.dataset.platformMotionIndex || '0', 10);

                element.animate(
                    [
                        { opacity: 0, transform: 'translate3d(0, 40px, 0) scale(.985)' },
                        { opacity: 1, transform: 'translate3d(0, 0, 0) scale(1)' }
                    ],
                    {
                        duration: 820,
                        delay: Math.min(index, 5) * 70,
                        easing: 'cubic-bezier(.22,1,.36,1)',
                        fill: 'both'
                    }
                );

                observer.unobserve(element);
            });
        }, { threshold: 0.14, rootMargin: '0px 0px -8% 0px' });

        targets.forEach(function (element, index) {
            element.dataset.platformMotionReveal = '1';
            element.dataset.platformMotionIndex = String(index % 6);
            observer.observe(element);
        });

        return true;
    }

    function initReveals() {
        var targets = revealTargets();
        if (!targets.length) {
            return;
        }

        if (animateWithGsap(targets)) {
            return;
        }

        animateWithObserver(targets);
    }

    onReady(function () {
        var body = document.body;
        if (!body) {
            return;
        }

        hydrateThemeButtons();
        assignMotionClasses();
        bindPointerGlow();

        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            body.classList.add('platform-motion-ready');
            return;
        }

        animateIntro();
        initReveals();
        body.classList.add('platform-motion-ready');
    });
})();
