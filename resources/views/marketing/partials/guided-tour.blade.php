@php
    $guidedTourBootstrap = auth()->check()
        ? app(\App\Services\GuidedTour\PlatformGuidedTourService::class)->bootstrapFor(auth()->user())
        : ['enabled' => false];
@endphp

@if(($guidedTourBootstrap['enabled'] ?? false) === true)
    <style>
        .erah-guided-tour-root { position: fixed; inset: 0; z-index: 3100; pointer-events: none; }
        .erah-guided-tour-root[hidden] { display: none !important; }
        .erah-guided-tour-backdrop { position: absolute; inset: 0; background: rgba(5, 7, 11, .72); backdrop-filter: blur(4px); }
        .erah-guided-tour-spotlight {
            position: absolute;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, .22);
            box-shadow: 0 0 0 9999px rgba(5, 7, 11, .6), 0 24px 60px rgba(0, 0, 0, .34), inset 0 0 0 1px rgba(216, 7, 7, .28);
            background: transparent;
            opacity: 0;
            transition: opacity .2s ease;
        }
        .erah-guided-tour-spotlight.is-visible { opacity: 1; }
        .erah-guided-tour-card {
            position: fixed;
            width: min(420px, calc(100vw - 32px));
            padding: 24px 24px 22px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: radial-gradient(circle at top left, rgba(216, 7, 7, .18), transparent 42%), linear-gradient(180deg, rgba(17, 19, 26, .98), rgba(7, 8, 12, .98));
            box-shadow: 0 30px 70px rgba(0, 0, 0, .4);
            color: #fff;
            pointer-events: auto;
        }
        .erah-guided-tour-card.is-centered { top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); }
        .erah-guided-tour-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, .58);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .2em;
            text-transform: uppercase;
        }
        .erah-guided-tour-kicker::before { content: ""; width: 24px; height: 1px; background: rgba(216, 7, 7, .85); }
        .erah-guided-tour-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-top: 14px; }
        .erah-guided-tour-title { margin: 0; font-size: 29px; line-height: .96; font-family: "Big Shoulders Display", sans-serif; text-transform: uppercase; }
        .erah-guided-tour-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .8);
            font-size: 18px;
            cursor: pointer;
            transition: border-color .22s ease, background-color .22s ease, color .22s ease;
        }
        .erah-guided-tour-close:hover { border-color: rgba(216, 7, 7, .42); background: rgba(216, 7, 7, .12); color: #fff; }
        .erah-guided-tour-copy { margin: 14px 0 0; color: rgba(255, 255, 255, .8); line-height: 1.74; }
        .erah-guided-tour-note {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, .08);
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .72);
            line-height: 1.6;
        }
        .erah-guided-tour-progress { margin-top: 18px; }
        .erah-guided-tour-progress-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255, 255, 255, .6);
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .erah-guided-tour-progress-track { margin-top: 10px; width: 100%; height: 8px; overflow: hidden; border-radius: 999px; background: rgba(255, 255, 255, .08); }
        .erah-guided-tour-progress-track > span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #d80707 0%, #ff5a36 100%); }
        .erah-guided-tour-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 22px; }
        .erah-guided-tour-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: border-color .22s ease, background-color .22s ease, opacity .22s ease;
        }
        .erah-guided-tour-btn:hover:not(:disabled) { border-color: rgba(216, 7, 7, .4); background: rgba(216, 7, 7, .12); }
        .erah-guided-tour-btn:disabled { opacity: .38; cursor: not-allowed; }
        .erah-guided-tour-btn--primary { border-color: rgba(216, 7, 7, .42); background: linear-gradient(135deg, rgba(216, 7, 7, .95), rgba(139, 7, 7, .95)); }
        .erah-guided-tour-btn--ghost { background: transparent; }
        .erah-guided-tour-resume {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 3050;
            width: min(360px, calc(100vw - 24px));
            padding: 20px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: radial-gradient(circle at top left, rgba(216, 7, 7, .16), transparent 40%), linear-gradient(180deg, rgba(14, 15, 21, .98), rgba(7, 8, 12, .98));
            box-shadow: 0 24px 60px rgba(0, 0, 0, .34);
            color: #fff;
        }
        .erah-guided-tour-resume[hidden] { display: none !important; }
        .erah-guided-tour-resume p { margin: 10px 0 0; color: rgba(255, 255, 255, .76); line-height: 1.6; }
        .erah-guided-tour-resume-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        @media (max-width: 767.98px) {
            .erah-guided-tour-resume { right: 12px; bottom: 12px; width: calc(100vw - 24px); }
            .erah-guided-tour-card { width: calc(100vw - 24px); padding: 20px 18px 18px; border-radius: 22px; }
            .erah-guided-tour-title { font-size: 24px; }
        }
    </style>

    <script id="erah-guided-tour-data" type="application/json">@json($guidedTourBootstrap)</script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dataElement = document.getElementById('erah-guided-tour-data');
            if (!dataElement) return;

            var bootstrap = {};
            try { bootstrap = JSON.parse(dataElement.textContent || '{}'); } catch (error) { return; }
            if (!bootstrap.enabled) return;

            var steps = Array.isArray(bootstrap.steps) ? bootstrap.steps : [];
            var state = bootstrap.state || {};
            var endpoints = bootstrap.endpoints || {};
            var csrf = bootstrap.csrf || '';
            var requestBusy = false;
            var resumeDismissedKey = 'erah-guided-tour-resume-dismissed';

            var root = document.createElement('div');
            root.className = 'erah-guided-tour-root';
            root.hidden = true;
            root.innerHTML = '<div class="erah-guided-tour-backdrop"></div><div class="erah-guided-tour-spotlight"></div><section class="erah-guided-tour-card" role="dialog" aria-modal="true" aria-live="polite"><div class="erah-guided-tour-kicker" data-tour-kicker></div><div class="erah-guided-tour-head"><h2 class="erah-guided-tour-title" data-tour-title></h2><button type="button" class="erah-guided-tour-close" data-tour-close aria-label="Quitter la visite">&times;</button></div><p class="erah-guided-tour-copy" data-tour-copy></p><div class="erah-guided-tour-note" data-tour-note hidden></div><div class="erah-guided-tour-progress"><div class="erah-guided-tour-progress-row"><span data-tour-progress-label></span><span data-tour-progress-percent></span></div><div class="erah-guided-tour-progress-track"><span data-tour-progress-bar></span></div></div><div class="erah-guided-tour-actions"><button type="button" class="erah-guided-tour-btn erah-guided-tour-btn--ghost" data-tour-previous>Precedent</button><button type="button" class="erah-guided-tour-btn" data-tour-pause>Quitter</button><button type="button" class="erah-guided-tour-btn erah-guided-tour-btn--primary" data-tour-next>Suivant</button></div></section>';
            document.body.appendChild(root);

            var resumeCard = document.createElement('aside');
            resumeCard.className = 'erah-guided-tour-resume';
            resumeCard.hidden = true;
            resumeCard.innerHTML = '<div class="erah-guided-tour-kicker">Visite guidee</div><h3 class="erah-guided-tour-title" style="font-size:22px;margin-top:12px;">Parcours en attente</h3><p data-tour-resume-copy></p><div class="erah-guided-tour-resume-actions"><button type="button" class="erah-guided-tour-btn erah-guided-tour-btn--primary" data-tour-resume>Reprendre</button><button type="button" class="erah-guided-tour-btn" data-tour-restart>Recommencer</button><button type="button" class="erah-guided-tour-btn erah-guided-tour-btn--ghost" data-tour-resume-dismiss>Plus tard</button></div>';
            document.body.appendChild(resumeCard);

            var spotlight = root.querySelector('.erah-guided-tour-spotlight');
            var card = root.querySelector('.erah-guided-tour-card');
            var kickerElement = root.querySelector('[data-tour-kicker]');
            var titleElement = root.querySelector('[data-tour-title]');
            var copyElement = root.querySelector('[data-tour-copy]');
            var noteElement = root.querySelector('[data-tour-note]');
            var progressLabelElement = root.querySelector('[data-tour-progress-label]');
            var progressPercentElement = root.querySelector('[data-tour-progress-percent]');
            var progressBarElement = root.querySelector('[data-tour-progress-bar]');
            var previousButton = root.querySelector('[data-tour-previous]');
            var pauseButton = root.querySelector('[data-tour-pause]');
            var nextButton = root.querySelector('[data-tour-next]');
            var closeButton = root.querySelector('[data-tour-close]');
            var resumeCopyElement = resumeCard.querySelector('[data-tour-resume-copy]');
            var resumeButton = resumeCard.querySelector('[data-tour-resume]');
            var restartButton = resumeCard.querySelector('[data-tour-restart]');
            var dismissResumeButton = resumeCard.querySelector('[data-tour-resume-dismiss]');

            function currentIndex() {
                var index = Number(state.current_step_index || 0);
                return Number.isNaN(index) || index < 0 ? 0 : index;
            }

            function getCurrentStep() { return steps[currentIndex()] || steps[0] || null; }

            function pathnameFromUrl(url) {
                try { return new URL(url || window.location.pathname, window.location.origin).pathname; }
                catch (error) { return window.location.pathname; }
            }

            function progressPercent() {
                if (!steps.length) return 0;
                var value = state.is_completed ? steps.length : (currentIndex() + 1);
                return Math.max(0, Math.min(100, Math.round((value / steps.length) * 100)));
            }

            function shouldAutoOpen() {
                var step = getCurrentStep();
                return !!step && state.status === 'in_progress' && !state.is_paused && pathnameFromUrl(step.route) === window.location.pathname;
            }

            function shouldShowResumePrompt() {
                var step = getCurrentStep();
                if (!step || state.status !== 'in_progress') return false;
                if (window.sessionStorage.getItem(resumeDismissedKey) === '1') return false;
                if (state.is_paused) return true;
                return pathnameFromUrl(step.route) !== window.location.pathname;
            }

            function hideResumePrompt() { resumeCard.hidden = true; }

            function showResumePrompt() {
                if (!shouldShowResumePrompt()) { hideResumePrompt(); return; }
                var step = getCurrentStep();
                resumeCopyElement.textContent = 'Votre progression est sauvegardee. Reprenez a l etape ' + (currentIndex() + 1) + ' sur ' + steps.length + ' : ' + step.title + '.';
                resumeCard.hidden = false;
            }

            function hideOverlay() {
                root.hidden = true;
                card.classList.remove('is-centered');
                spotlight.classList.remove('is-visible');
            }

            function renderPosition(target) {
                if (!target) {
                    spotlight.classList.remove('is-visible');
                    card.classList.add('is-centered');
                    card.style.top = '50%';
                    card.style.left = '50%';
                    card.style.transform = 'translate(-50%, -50%)';
                    return;
                }

                var rect = target.getBoundingClientRect();
                spotlight.classList.add('is-visible');
                spotlight.style.top = Math.max(12, rect.top - 10) + 'px';
                spotlight.style.left = Math.max(12, rect.left - 10) + 'px';
                spotlight.style.width = Math.max(120, rect.width + 20) + 'px';
                spotlight.style.height = Math.max(84, rect.height + 20) + 'px';

                card.classList.remove('is-centered');
                card.style.transform = 'none';
                card.style.top = '0px';
                card.style.left = '0px';

                window.requestAnimationFrame(function () {
                    var cardRect = card.getBoundingClientRect();
                    var top = rect.bottom + 18;
                    var left = rect.left;
                    if (top + cardRect.height > window.innerHeight - 16) top = rect.top - cardRect.height - 18;
                    if (top < 16) top = 16;
                    if (left + cardRect.width > window.innerWidth - 16) left = window.innerWidth - cardRect.width - 16;
                    if (left < 16) left = 16;
                    card.style.top = top + 'px';
                    card.style.left = left + 'px';
                });
            }

            function renderStep() {
                var step = getCurrentStep();
                if (!step) { hideOverlay(); hideResumePrompt(); return; }

                var target = step.selector ? document.querySelector(step.selector) : null;
                var note = '';

                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                } else {
                    note = step.fallback_body || 'Le bloc cible de cette etape est introuvable sur cette page. Vous pouvez tout de meme continuer.';
                }

                kickerElement.textContent = 'Etape ' + step.step_number + ' sur ' + steps.length;
                titleElement.textContent = step.title || 'Visite guidee';
                copyElement.textContent = step.description || step.summary || '';
                progressLabelElement.textContent = step.progress_label || ('Etape ' + step.step_number);
                progressPercentElement.textContent = progressPercent() + '%';
                progressBarElement.style.width = progressPercent() + '%';
                previousButton.disabled = currentIndex() <= 0;
                nextButton.textContent = currentIndex() >= steps.length - 1 ? 'Terminer' : 'Suivant';

                if (note) {
                    noteElement.hidden = false;
                    noteElement.textContent = note;
                } else {
                    noteElement.hidden = true;
                    noteElement.textContent = '';
                }

                root.hidden = false;
                hideResumePrompt();
                window.setTimeout(function () { renderPosition(target); }, target ? 260 : 0);
            }

            async function send(action) {
                if (requestBusy || !endpoints.update) return null;
                requestBusy = true;
                try {
                    var response = await window.fetch(endpoints.update, {
                        method: 'PATCH',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ action: action }),
                    });
                    if (!response.ok) throw new Error('Tour request failed');
                    var payload = await response.json();
                    state = payload.data.state || state;
                    return payload.data;
                } catch (error) {
                    return null;
                } finally {
                    requestBusy = false;
                }
            }

            async function startTour(mode) {
                var endpoint = mode === 'restart' ? endpoints.restart : endpoints.start;
                if (!endpoint || requestBusy) return;
                requestBusy = true;
                try {
                    var response = await window.fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({}),
                    });
                    if (!response.ok) throw new Error('Unable to start tour');
                    var payload = await response.json();
                    state = payload.data.state || state;
                    window.sessionStorage.removeItem(resumeDismissedKey);
                    var step = getCurrentStep();
                    if (step && pathnameFromUrl(step.route) !== window.location.pathname) { window.location.assign(step.route); return; }
                    renderStep();
                } catch (error) {
                    return;
                } finally {
                    requestBusy = false;
                }
            }

            async function continueTour() {
                var result = await send('resume');
                if (!result) return;
                window.sessionStorage.removeItem(resumeDismissedKey);
                var step = getCurrentStep();
                if (step && pathnameFromUrl(step.route) !== window.location.pathname) { window.location.assign(step.route); return; }
                renderStep();
            }

            async function move(action) {
                var result = await send(action);
                if (!result) return;
                if (state.is_completed) { renderCompletion(); return; }
                var step = getCurrentStep();
                if (step && pathnameFromUrl(step.route) !== window.location.pathname) { window.location.assign(step.route); return; }
                renderStep();
            }

            async function pauseTour() { await send('pause'); hideOverlay(); showResumePrompt(); }

            function renderCompletion() {
                spotlight.classList.remove('is-visible');
                root.hidden = false;
                card.classList.add('is-centered');
                card.style.top = '50%';
                card.style.left = '50%';
                card.style.transform = 'translate(-50%, -50%)';
                kickerElement.textContent = 'Visite terminee';
                titleElement.textContent = 'Vous avez fait le tour';
                copyElement.textContent = 'Le parcours est complete. Vous pouvez revenir au help center pour le relancer depuis le debut quand vous voulez.';
                noteElement.hidden = true;
                progressLabelElement.textContent = steps.length + ' etapes validees';
                progressPercentElement.textContent = '100%';
                progressBarElement.style.width = '100%';
                previousButton.disabled = true;
                pauseButton.textContent = 'Fermer';
                nextButton.textContent = 'Recommencer';
            }

            previousButton.addEventListener('click', function () { move('previous'); });
            nextButton.addEventListener('click', function () { if (state.is_completed) { startTour('restart'); return; } move('next'); });
            pauseButton.addEventListener('click', function () { if (state.is_completed) { hideOverlay(); showResumePrompt(); return; } pauseTour(); });
            closeButton.addEventListener('click', function () {
                if (state.is_completed) { hideOverlay(); hideResumePrompt(); return; }
                pauseTour();
            });
            resumeButton.addEventListener('click', continueTour);
            restartButton.addEventListener('click', function () { startTour('restart'); });
            dismissResumeButton.addEventListener('click', function () { window.sessionStorage.setItem(resumeDismissedKey, '1'); hideResumePrompt(); });

            document.querySelectorAll('[data-guided-tour-action]').forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    var action = trigger.getAttribute('data-guided-tour-action') || 'start';
                    if (action === 'start') { startTour('start'); return; }
                    if (action === 'restart') { startTour('restart'); return; }
                    if (action === 'resume') continueTour();
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !root.hidden) pauseTour();
            });

            if (shouldAutoOpen()) {
                window.sessionStorage.removeItem(resumeDismissedKey);
                window.setTimeout(renderStep, 260);
            } else {
                showResumePrompt();
            }
        });
    </script>
@endif
