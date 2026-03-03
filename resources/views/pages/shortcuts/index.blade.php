@extends('layouts.app')

@section('title', 'Raccourcis Plateforme')

@section('content')
    @php
        $selectedKeys = array_column($current, 'key');
        $selectedOrderMap = [];
        foreach ($selectedKeys as $index => $key) {
            $selectedOrderMap[$key] = $index + 1;
        }
    @endphp

    <section class="section">
        <h1>Raccourcis Plateforme</h1>
        <p class="meta">
            Choisis de {{ $minShortcuts }} a {{ $maxShortcuts }} raccourcis pour le menu "Plateforme".
        </p>

        <div class="actions">
            <span class="badge badge-accent">
                Selection actuelle: <strong id="shortcut-selected-count">{{ count($selectedKeys) }}</strong> / {{ $maxShortcuts }}
            </span>
        </div>

        <form method="POST" action="{{ route('app.shortcuts.update') }}">
            @csrf

            <div class="grid grid-2">
                @foreach($available as $item)
                    @php($key = $item['key'])
                    <div class="ui-card">
                        <div class="actions" style="justify-content: space-between; margin-top: 0;">
                            <label style="display: inline-flex; gap: 8px; align-items: center; margin-bottom: 0; font-weight: 600;">
                                <input
                                    type="checkbox"
                                    name="shortcuts[]"
                                    value="{{ $key }}"
                                    @checked(in_array($key, $selectedKeys, true))
                                    data-shortcut-toggle
                                >
                                <span>{{ $item['label'] }}</span>
                            </label>

                            <span class="badge {{ $item['requires_auth'] ? 'badge-accent' : '' }}">
                                {{ $item['requires_auth'] ? 'auth' : 'public' }}
                            </span>
                        </div>

                        <div class="actions">
                            <label style="margin-bottom: 0;">
                                Ordre
                                <input
                                    type="number"
                                    name="orders[{{ $key }}]"
                                    value="{{ $selectedOrderMap[$key] ?? ($loop->index + 1) }}"
                                    min="1"
                                    max="{{ $maxShortcuts }}"
                                    data-shortcut-order
                                    style="max-width: 90px;"
                                >
                            </label>

                            <button type="button" class="btn btn-secondary btn-sm" data-order-step="-1">&uarr;</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-order-step="1">&darr;</button>
                        </div>

                        <p class="meta" style="word-break: break-all;">{{ $item['url'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Enregistrer mes raccourcis</button>
            </div>
        </form>

        <form method="POST" action="{{ route('app.shortcuts.reset') }}" class="actions">
            @csrf
            <button type="submit" class="btn btn-outline">Reinitialiser par defaut</button>
        </form>
    </section>

    <script>
        (function () {
            const toggles = Array.from(document.querySelectorAll('[data-shortcut-toggle]'));
            const countEl = document.getElementById('shortcut-selected-count');
            const maxCount = {{ (int) $maxShortcuts }};
            const minCount = {{ (int) $minShortcuts }};

            function updateCount() {
                const checked = toggles.filter((el) => el.checked).length;
                countEl.textContent = String(checked);

                toggles.forEach((toggle) => {
                    if (!toggle.checked && checked >= maxCount) {
                        toggle.disabled = true;
                    } else {
                        toggle.disabled = false;
                    }
                });
            }

            toggles.forEach((toggle) => {
                toggle.addEventListener('change', updateCount);
            });

            document.querySelectorAll('[data-order-step]').forEach((button) => {
                button.addEventListener('click', function () {
                    const wrapper = button.closest('.ui-card');
                    if (!wrapper) return;

                    const input = wrapper.querySelector('[data-shortcut-order]');
                    if (!input) return;

                    const delta = Number(button.getAttribute('data-order-step') || 0);
                    const current = Number(input.value || 1);
                    const next = Math.max(1, Math.min(maxCount, current + delta));
                    input.value = String(next);
                });
            });

            const form = document.querySelector('form[action="{{ route('app.shortcuts.update') }}"]');
            if (form) {
                form.addEventListener('submit', function (event) {
                    const checked = toggles.filter((el) => el.checked).length;
                    if (checked < minCount || checked > maxCount) {
                        event.preventDefault();
                        alert(`Selection invalide: ${minCount} minimum et ${maxCount} maximum.`);
                    }
                });
            }

            updateCount();
        })();
    </script>
@endsection


