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
            width: min(400px, calc(100vw - 24px));
            display: grid;
            gap: 12px;
            z-index: 2950;
            pointer-events: none;
        }
        .mission-live-toast {
            padding: 18px;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, .12);
            background:
                radial-gradient(circle at top left, rgba(216, 7, 7, .18), transparent 42%),
                linear-gradient(180deg, rgba(14, 15, 21, .98), rgba(7, 8, 12, .98));
            box-shadow: 0 22px 60px rgba(0, 0, 0, .38);
            color: #fff;
            pointer-events: auto;
            transition: opacity .2s ease, transform .2s ease;
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
            margin: 10px 0 0;
            font-size: 28px;
            line-height: .95;
            font-family: "Big Shoulders Display", sans-serif;
            text-transform: uppercase;
        }
        .mission-live-toast-close {
            flex: 0 0 auto;
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .76);
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
        }
        .mission-live-toast-body {
            margin: 12px 0 0;
            color: rgba(255, 255, 255, .8);
            line-height: 1.55;
        }
        .mission-live-toast-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .mission-live-toast-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .84);
        }
        .mission-live-toast-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }
        .mission-live-toast-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }
        .mission-live-toast-link:hover {
            border-color: rgba(216, 7, 7, .38);
            background: rgba(216, 7, 7, .14);
        }
        @media (max-width: 767.98px) {
            .mission-live-stack {
                left: 12px;
                right: 12px;
                bottom: 12px;
                width: auto;
                gap: 8px;
            }
            .mission-live-toast {
                padding: 14px;
                border-radius: 18px;
            }
            .mission-live-toast-title {
                margin-top: 8px;
                font-size: 19px;
                line-height: 1;
            }
            .mission-live-toast-body {
                margin-top: 10px;
                font-size: 14px;
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
                min-height: 40px;
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

            var isCompactMobile = window.matchMedia('(max-width: 767.98px)').matches;
            var cursorKey = 'erah-mission-toast-last-id:' + bootstrap.user_id;
            var pendingKey = 'erah-mission-toast-pending:' + bootstrap.user_id;
            var stack = document.createElement('div');
            stack.className = 'mission-live-stack';
            document.body.appendChild(stack);

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

                    var now = Date.now();
                    var active = pending.filter(function (item) {
                        return item
                            && typeof item === 'object'
                            && item.notification
                            && Number(item.expires_at || 0) > now;
                    });

                    window.sessionStorage.setItem(pendingKey, JSON.stringify(active));

                    return active;
                } catch (error) {
                    return [];
                }
            }

            function rememberToast(notification, expiresAt) {
                if (!notification || !notification.id) {
                    return;
                }

                var pending = readPendingToasts().filter(function (item) {
                    return Number((item.notification || {}).id || 0) !== Number(notification.id || 0);
                });

                pending.push({
                    notification: notification,
                    expires_at: Number(expiresAt || 0),
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
                }, 180);
            }

            function clearCompactMobileStack(exceptNotificationId) {
                if (!isCompactMobile) {
                    return;
                }

                stack.querySelectorAll('.mission-live-toast').forEach(function (node) {
                    var existingId = Number(node.dataset.notificationId || 0);
                    if (exceptNotificationId > 0 && existingId === exceptNotificationId) {
                        return;
                    }

                    forgetToast(existingId);
                    node.remove();
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

                clearCompactMobileStack(notificationId);

                var expiresAt = Number(options.expiresAt || (Date.now() + 7600));
                var remainingDuration = expiresAt - Date.now();
                if (remainingDuration <= 250) {
                    forgetToast(notificationId);
                    return;
                }

                var kind = toastKind(notification);
                var meta = buildMeta(notification);
                var title = String(notification.title || 'Mission');
                var body = String(notification.message || '');

                var toast = document.createElement('article');
                toast.className = 'mission-live-toast is-' + kind;
                toast.dataset.notificationId = String(notificationId);
                toast.innerHTML =
                    '<div class="mission-live-toast-head">' +
                        '<div>' +
                            '<span class="mission-live-toast-kicker">Mission ERAH</span>' +
                            '<h3 class="mission-live-toast-title"></h3>' +
                        '</div>' +
                        '<button type="button" class="mission-live-toast-close" aria-label="Fermer">&times;</button>' +
                    '</div>' +
                    '<p class="mission-live-toast-body"></p>' +
                    '<div class="mission-live-toast-meta"></div>' +
                    '<div class="mission-live-toast-actions">' +
                        '<a class="mission-live-toast-link" href="' + bootstrap.missions_url + '">Voir mes missions</a>' +
                    '</div>';

                toast.querySelector('.mission-live-toast-title').textContent = title;
                toast.querySelector('.mission-live-toast-body').textContent = body;

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

                rememberToast(notification, expiresAt);
                stack.appendChild(toast);
                window.setTimeout(function () {
                    removeToast(toast);
                }, remainingDuration);
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

                    var notificationsToShow = isCompactMobile ? notifications.slice(-1) : notifications;

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

            if (isCompactMobile && pendingToasts.length > 1) {
                pendingToasts = pendingToasts.slice(-1);
            }

            pendingToasts.forEach(function (item) {
                    showToast(item.notification || {}, {
                        expiresAt: Number(item.expires_at || 0),
                    });
                });

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
