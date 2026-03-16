@php
    $items = collect($items ?? []);
    $title = $title ?? 'Notifications';
    $subtitle = $subtitle ?? null;
@endphp

<section class="notif-stream tt-anim-fadeinup">
    <div class="notif-stream-head">
        <div>
            <h2>{{ $title }}</h2>
            @if($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
        </div>
        <span class="notif-stream-count">{{ $items->count() }} notification(s)</span>
    </div>

    @foreach($items->groupBy(fn ($notification) => (string) optional($notification->created_at)->format('Y-m-d')) as $dateKey => $dayItems)
        <div class="notif-day-group">
            <span class="notif-day-label">
                {{ optional($dayItems->first()->created_at)->format('d/m/Y') ?: 'Date inconnue' }}
            </span>

            <div class="notif-list">
                @foreach($dayItems as $notification)
                    @php
                        $categoryKey = (string) $notification->category;
                        $categoryLabel = $categoryLabels[$categoryKey] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $categoryKey));
                        $toneClass = $categoryToneMap[$categoryKey] ?? '';
                        $iconClass = $categoryIconMap[$categoryKey] ?? 'fa-solid fa-bell';
                        $isUnread = $notification->read_at === null;

                        $payload = is_array($notification->data) ? $notification->data : [];
                        $actionUrlRaw = (isset($payload['url']) && is_string($payload['url'])) ? trim((string) $payload['url']) : '';
                        $actionUrl = '';
                        if (
                            $actionUrlRaw !== ''
                            && (
                                preg_match('/^https?:\/\//i', $actionUrlRaw) === 1
                                || str_starts_with($actionUrlRaw, '/')
                            )
                        ) {
                            $actionUrl = $actionUrlRaw;
                        }
                        $actionLabel = (isset($payload['cta_label']) && is_string($payload['cta_label']) && trim((string) $payload['cta_label']) !== '')
                            ? trim((string) $payload['cta_label'])
                            : 'Ouvrir';
                        $isExternalAction = $actionUrl !== '' && preg_match('/^https?:\/\//i', $actionUrl) === 1;
                    @endphp
                    <article class="notif-item {{ $isUnread ? 'is-unread' : '' }}">
                        <header class="notif-item-head">
                            <div class="notif-item-meta">
                                <span class="notif-category-badge {{ $toneClass }}">
                                    <i class="{{ $iconClass }}"></i>
                                    {{ $categoryLabel }}
                                </span>
                                @if($isUnread)
                                    <span class="notif-state-badge">Nouveau</span>
                                @endif
                            </div>
                            <time class="notif-time">{{ optional($notification->created_at)->format('d/m/Y H:i') ?? '-' }}</time>
                        </header>

                        <h3 class="notif-title">{{ (string) $notification->title }}</h3>
                        <p class="notif-message">{{ (string) ($notification->message ?: 'Notification sans message detaille.') }}</p>

                        <div class="notif-item-actions">
                            @if($actionUrl !== '')
                                <a href="{{ $actionUrl }}"
                                    class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item"
                                    @if($isExternalAction) target="_blank" rel="noopener" @endif>
                                    <span data-hover="{{ $actionLabel }}">{{ $actionLabel }}</span>
                                </a>
                            @endif

                            @if($isUnread)
                                <form method="POST" action="{{ route($readRouteName, $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                        <span data-hover="Marquer lue">Marquer lue</span>
                                    </button>
                                </form>
                            @else
                                <span class="notif-muted">Deja lue</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endforeach
</section>