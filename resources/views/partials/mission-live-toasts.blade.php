@php
    $missionToastBootstrap = null;

    if (auth()->check()) {
        $missionToastBootstrap = [
            'user_id' => auth()->id(),
            'latest_id' => (int) \App\Models\Notification::query()
                ->where('user_id', auth()->id())
                ->where('category', \App\Domain\Notifications\Enums\NotificationCategory::MISSION->value)
                ->max('id'),
            'endpoint' => route('notifications.live', [], false),
            'missions_url' => request()->routeIs('app.*')
                ? route('app.missions.index', [], false)
                : route('missions.index', [], false),
        ];
    }
@endphp

@if($missionToastBootstrap)
    <style>
        .mission-live-stack {
            position: fixed;
            left: 16px;
            bottom: 18px;
            width: min(440px, calc(100vw - 24px));
            max-height: min(78vh, calc(100vh - 72px));
            z-index: 2950;
            pointer-events: none;
        }
        .mission-live-stack-shell {
            display: flex;
            flex-direction: column;
            max-height: inherit;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(8, 9, 14, .9);
            box-shadow: 0 24px 70px rgba(0, 0, 0, .42);
            overflow: hidden;
            pointer-events: auto;
            backdrop-filter: blur(10px);
        }
        .mission-live-stack[data-empty="true"] {
            display: none;
        }
        .mission-live-stack-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            background: linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }
        .mission-live-stack-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, .58);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .18em;
        }
        .mission-live-stack-kicker::before {
            content: '';
            width: 18px;
            height: 1px;
            background: rgba(216, 7, 7, .88);
        }
        .mission-live-stack-title {
            margin: 8px 0 0;
            font-size: 17px;
            line-height: 1.1;
            font-weight: 700;
            color: #fff;
        }
        .mission-live-stack-subtitle {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, .64);
            font-size: 12px;
            line-height: 1.45;
        }
        .mission-live-stack-tools {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .mission-live-stack-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .05);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }
        .mission-live-stack-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }
        .mission-live-stack-link:hover {
            border-color: rgba(216, 7, 7, .38);
            background: rgba(216, 7, 7, .14);
        }
        .mission-live-stack-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding: 12px 8px 10px;
            scroll-behavior: smooth;
        }
        .mission-live-stack-list::-webkit-scrollbar {
            width: 6px;
        }
        .mission-live-stack-list::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
        }
        .mission-live-stack-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .mission-live-toast {
            padding: 14px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, .12);
            background:
                radial-gradient(circle at top left, rgba(216, 7, 7, .18), transparent 42%),
                linear-gradient(180deg, rgba(14, 15, 21, .98), rgba(7, 8, 12, .98));
            box-shadow: 0 22px 60px rgba(0, 0, 0, .38);
            color: #fff;
            pointer-events: auto;
            transition: opacity .2s ease, transform .2s ease;
        }
        .mission-live-toast.is-hidden {
            display: none;
        }
        .mission-live-toast.is-leaving {
            opacity: 0;
            transform: translateY(10px);
        }
        .mission-live-toast.is-progress {
            border-color: rgba(255, 214, 102, .28);
        }
        .mission-live-toast.is-completed {
            border-color: rgba(104, 220, 150, .3);
            background:
                radial-gradient(circle at top left, rgba(56, 189, 120, .18), transparent 42%),
                linear-gradient(180deg, rgba(14, 15, 21, .98), rgba(7, 8, 12, .98));
        }
        .mission-live-toast.is-claim {
            border-color: rgba(126, 196, 255, .34);
        }
        .mission-live-toast-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }
        .mission-live-toast-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, .6);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .16em;
        }
        .mission-live-toast-kicker::before {
            content: '';
            width: 22px;
            height: 1px;
            background: rgba(216, 7, 7, .88);
        }
        .mission-live-toast-title {
            margin: 8px 0 0;
            font-size: 24px;
            line-height: .96;
            font-family: "Big Shoulders Display", sans-serif;
            text-transform: uppercase;
        }
        .mission-live-toast-status {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .88);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .mission-live-toast-status.is-completed {
            border-color: rgba(104, 220, 150, .35);
            color: #d8ffe5;
        }
        .mission-live-toast-status.is-claim {
            border-color: rgba(126, 196, 255, .35);
            color: #dff1ff;
        }
        .mission-live-toast-status.is-progress {
            border-color: rgba(255, 214, 102, .32);
            color: #fff0c2;
        }
        .mission-live-toast-close {
            flex: 0 0 auto;
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .76);
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
        }
        .mission-live-toast-body {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .8);
            line-height: 1.45;
            font-size: 15px;
        }
        .mission-live-toast-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 12px;
        }
        .mission-live-toast-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 10px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .84);
        }
        .mission-live-toast-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }
        .mission-live-toast-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }
        .mission-live-toast-link:hover {
            border-color: rgba(216, 7, 7, .38);
            background: rgba(216, 7, 7, .14);
        }
        .mission-live-stack-footer {
            display: none;
            padding: 0 8px 10px;
        }
        .mission-live-stack-footer.is-visible {
            display: block;
        }
        .mission-live-stack-toggle {
            width: 100%;
            min-height: 38px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            cursor: pointer;
            pointer-events: auto;
        }
        .mission-live-stack-toggle:hover {
            border-color: rgba(216, 7, 7, .38);
            background: rgba(216, 7, 7, .14);
        }
        @media (max-width: 767.98px) {
            .mission-live-stack {
                left: 12px;
                right: 12px;
                bottom: 12px;
                width: auto;
                max-height: min(82vh, calc(100vh - 40px));
            }
            .mission-live-stack-header {
                padding: 12px 12px 10px;
            }
            .mission-live-stack-title {
                font-size: 15px;
            }
            .mission-live-stack-subtitle {
                font-size: 11px;
            }
            .mission-live-stack-tools {
                width: 100%;
                justify-content: space-between;
            }
            .mission-live-stack-link {
                flex: 1 1 auto;
            }
            .mission-live-toast {
                padding: 12px;
                border-radius: 16px;
            }
            .mission-live-toast-title {
                margin-top: 6px;
                font-size: 18px;
                line-height: 1;
            }
            .mission-live-toast-body {
                margin-top: 8px;
                font-size: 13px;
                line-height: 1.45;
            }
            .mission-live-toast-pill {
                font-size: 10px;
                letter-spacing: .06em;
            }
            .mission-live-toast-actions {
                margin-top: 12px;
            }
            .mission-live-toast-link {
                width: 100%;
                min-height: 36px;
                font-size: 12px;
            }
        }
    </style>

    <script id="mission-live-toast-data" type="application/json">@json($missionToastBootstrap)</script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dataElement = document.getElementById('mission-live-toast-data');
            if (!dataElement || !window.fetch) {
                return;
            }

            var bootstrap = {};
            try {
                bootstrap = JSON.parse(dataElement.textContent || '{}');
            } catch (error) {
                return;
            }

            if (!bootstrap.endpoint || !bootstrap.user_id) {
                return;
            }

            var cursorKey = 'erah-mission-toast-last-id:' + bootstrap.user_id;
            var pendingKey = 'erah-mission-toast-pending:' + bootstrap.user_id;
            var visibleToastLimit = 3;
            var isExpanded = false;
            var stack = document.createElement('div');
            stack.className = 'mission-live-stack';
            stack.dataset.empty = 'true';
            stack.innerHTML =
                '<div class="mission-live-stack-shell">' +
                    '<div class="mission-live-stack-header">' +
                        '<div>' +
                            '<span class="mission-live-stack-kicker">Notifications missions</span>' +
                            '<p class="mission-live-stack-title">Suivi mission en direct</p>' +
                            '<p class="mission-live-stack-subtitle">Les notifications les plus recentes restent en haut. Faites defiler pour voir l historique.</p>' +
                        '</div>' +
                        '<div class="mission-live-stack-tools">' +
                            '<span class="mission-live-stack-count" data-role="mission-toast-count">0</span>' +
                            '<a class="mission-live-stack-link" href="' + bootstrap.missions_url + '">Missions</a>' +
                        '</div>' +
                    '</div>' +
                    '<div class="mission-live-stack-list" data-role="mission-toast-list"></div>' +
                    '<div class="mission-live-stack-footer" data-role="mission-toast-footer">' +
                        '<button type="button" class="mission-live-stack-toggle" data-role="mission-toast-toggle">Voir les suivantes</button>' +
                    '</div>' +
                '</div>';
            document.body.appendChild(stack);

            var list = stack.querySelector('[data-role="mission-toast-list"]');
            var countNode = stack.querySelector('[data-role="mission-toast-count"]');
            var footerNode = stack.querySelector('[data-role="mission-toast-footer"]');
            var toggleNode = stack.querySelector('[data-role="mission-toast-toggle"]');

            function buildToastPresentation(notification) {
                var data = notification.data || {};
                var kind = toastKind(notification);

                if (kind === 'completed') {
                    return {
                        kind: kind,
                        kicker: 'Mission reussie',
                        status: 'Validee',
                        title: String(notification.title || '').trim().toLowerCase() === 'mission terminee'
                            ? 'Mission reussie'
                            : String(notification.title || 'Mission reussie'),
                    };
                }

                if (kind === 'claim') {
                    return {
                        kind: kind,
                        kicker: 'Recompense disponible',
                        status: 'A reclamer',
                        title: String(notification.title || 'Mission terminee'),
                    };
                }

                return {
                    kind: kind,
                    kicker: 'Mission ERAH',
                    status: 'En cours',
                    title: String(notification.title || 'Mission en progression'),
                };
            }

            function updateToastVisibility() {
                if (!list) {
                    return;
                }

                var toasts = Array.prototype.slice.call(list.children || []);
                var hiddenCount = 0;

                toasts.forEach(function (node, index) {
                    var shouldHide = !isExpanded && index >= visibleToastLimit;
                    node.classList.toggle('is-hidden', shouldHide);
                    if (shouldHide) {
                        hiddenCount += 1;
                    }
                });

                var shouldShowFooter = toasts.length > visibleToastLimit;
                if (footerNode) {
                    footerNode.classList.toggle('is-visible', shouldShowFooter);
                }

                if (toggleNode) {
                    toggleNode.textContent = isExpanded
                        ? 'Replier la pile'
                        : 'Voir les suivantes (' + hiddenCount + ')';
                }
            }

            var storedCursor = Number(window.localStorage.getItem(cursorKey) || 0);
            var cursor = Number.isNaN(storedCursor) || storedCursor <= 0
                ? Number(bootstrap.latest_id || 0)
                : storedCursor;

            window.localStorage.setItem(cursorKey, String(cursor));

            function readPendingToasts() {
                try {
                    var pending = JSON.parse(window.sessionStorage.getItem(pendingKey) || '[]');
                    if (!Array.isArray(pending)) {
                        return [];
                    }

                    var active = pending.filter(function (item) {
                        return item
                            && typeof item === 'object'
                            && item.notification
                            && Number((item.notification || {}).id || 0) > 0;
                    });

                    window.sessionStorage.setItem(pendingKey, JSON.stringify(active));

                    return active;
                } catch (error) {
                    return [];
                }
            }

            function rememberToast(notification) {
                if (!notification || !notification.id) {
                    return;
                }

                var pending = readPendingToasts().filter(function (item) {
                    return Number((item.notification || {}).id || 0) !== Number(notification.id || 0);
                });

                pending.push({
                    notification: notification,
                });

                window.sessionStorage.setItem(pendingKey, JSON.stringify(pending));
            }

            function forgetToast(notificationId) {
                if (!notificationId) {
                    return;
                }

                var pending = readPendingToasts().filter(function (item) {
                    return Number((item.notification || {}).id || 0) !== Number(notificationId || 0);
                });

                window.sessionStorage.setItem(pendingKey, JSON.stringify(pending));
            }

            function removeToast(node) {
                if (!node) {
                    return;
                }

                forgetToast(Number(node.dataset.notificationId || 0));

                node.classList.add('is-leaving');
                window.setTimeout(function () {
                    node.remove();
                    updateStackState();
                }, 180);
            }

            function updateStackState() {
                var count = list ? list.children.length : 0;
                stack.dataset.empty = count > 0 ? 'false' : 'true';
                if (countNode) {
                    countNode.textContent = String(count);
                }
                if (count <= visibleToastLimit) {
                    isExpanded = false;
                }
                updateToastVisibility();
            }

            function scrollStackToLatest() {
                window.requestAnimationFrame(function () {
                    if (list) {
                        list.scrollTop = 0;
                    }
                });
            }

            function toastKind(notification) {
                var data = notification.data || {};
                var kind = String(data.toast_kind || '').trim();

                if (kind !== '') {
                    return kind;
                }

                if (data.requires_claim) {
                    return 'claim';
                }

                return 'progress';
            }

            function buildMeta(notification) {
                var data = notification.data || {};
                var parts = [];

                if (Number(data.progress_count || 0) > 0 && Number(data.target_count || 0) > 0) {
                    parts.push(Number(data.progress_count) + '/' + Number(data.target_count));
                }

                if (Number(data.rewards_xp || 0) > 0) {
                    parts.push('+' + Number(data.rewards_xp) + ' XP');
                }

                if (Number(data.rewards_points || 0) > 0) {
                    parts.push('+' + Number(data.rewards_points) + ' points');
                }

                return parts;
            }

            function showToast(notification, options) {
                options = options || {};

                var notificationId = Number(notification.id || 0);
                if (notificationId > 0 && stack.querySelector('[data-notification-id="' + notificationId + '"]')) {
                    return;
                }

                var kind = toastKind(notification);
                var meta = buildMeta(notification);
                var presentation = buildToastPresentation(notification);
                var title = presentation.title;
                var body = String(notification.message || '');

                var toast = document.createElement('article');
                toast.className = 'mission-live-toast is-' + kind;
                toast.dataset.notificationId = String(notificationId);
                toast.innerHTML =
                    '<div class="mission-live-toast-head">' +
                        '<div>' +
                            '<span class="mission-live-toast-kicker"></span>' +
                            '<h3 class="mission-live-toast-title"></h3>' +
                        '</div>' +
                        '<div style="display:flex; align-items:center; gap:8px;">' +
                            '<span class="mission-live-toast-status"></span>' +
                            '<button type="button" class="mission-live-toast-close" aria-label="Fermer">&times;</button>' +
                        '</div>' +
                    '</div>' +
                    '<p class="mission-live-toast-body"></p>' +
                    '<div class="mission-live-toast-meta"></div>' +
                    '<div class="mission-live-toast-actions">' +
                        '<a class="mission-live-toast-link" href="' + bootstrap.missions_url + '">Voir mes missions</a>' +
                    '</div>';

                toast.querySelector('.mission-live-toast-kicker').textContent = presentation.kicker;
                toast.querySelector('.mission-live-toast-title').textContent = title;
                toast.querySelector('.mission-live-toast-body').textContent = body;
                var statusNode = toast.querySelector('.mission-live-toast-status');
                statusNode.textContent = presentation.status;
                statusNode.classList.add('is-' + presentation.kind);

                var metaContainer = toast.querySelector('.mission-live-toast-meta');
                if (meta.length === 0) {
                    metaContainer.remove();
                } else {
                    meta.forEach(function (item) {
                        var pill = document.createElement('span');
                        pill.className = 'mission-live-toast-pill';
                        pill.textContent = item;
                        metaContainer.appendChild(pill);
                    });
                }

                toast.querySelector('.mission-live-toast-close').addEventListener('click', function () {
                    removeToast(toast);
                });

                rememberToast(notification);
                list.prepend(toast);
                updateStackState();
                scrollStackToLatest();
            }

            async function poll() {
                try {
                    var response = await window.fetch(
                        bootstrap.endpoint + '?category=mission&after_id=' + encodeURIComponent(String(cursor)) + '&limit=5',
                        {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                        }
                    );

                    if (!response.ok) {
                        return;
                    }

                    var payload = await response.json();
                    var notifications = Array.isArray(payload.data) ? payload.data : [];
                    var latestId = Number((payload.meta || {}).latest_id || cursor || 0);

                    var notificationsToShow = notifications.slice().sort(function (left, right) {
                        return Number((left || {}).id || 0) - Number((right || {}).id || 0);
                    });

                    notificationsToShow.forEach(function (notification) {
                        showToast(notification);
                        cursor = Math.max(cursor, Number(notification.id || 0));
                    });

                    cursor = Math.max(cursor, latestId);
                    window.localStorage.setItem(cursorKey, String(cursor));
                } catch (error) {
                    return;
                }
            }

            var pendingToasts = readPendingToasts()
                .sort(function (left, right) {
                    return Number((left.notification || {}).id || 0) - Number((right.notification || {}).id || 0);
                });

            if (toggleNode) {
                toggleNode.addEventListener('click', function () {
                    isExpanded = !isExpanded;
                    updateToastVisibility();
                    if (!isExpanded) {
                        scrollStackToLatest();
                    }
                });
            }

            pendingToasts.forEach(function (item) {
                    showToast(item.notification || {}, {
                    });
                });

            updateStackState();

            poll();
            window.setInterval(poll, 3200);
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    poll();
                }
            });
        });
    </script>
@endif
