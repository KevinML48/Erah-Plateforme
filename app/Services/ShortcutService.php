<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserShortcut;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ShortcutService
{
    /**
     * @return array<int, array{key:string,label:string,url:string,requires_auth:bool}>
     */
    public function getForUser(?User $user): array
    {
        if ($user === null) {
            $catalog = $this->resolvedCatalog(includeProtected: false);
            $keys = $this->buildSelection(
                primary: (array) config('shortcuts.defaults_guest', []),
                allowed: array_keys($catalog),
                fallbackSets: []
            );

            return $this->mapKeysToItems($keys, $catalog);
        }

        $catalog = $this->resolvedCatalog(includeProtected: true);
        $keys = UserShortcut::query()
            ->where('user_id', $user->id)
            ->orderBy('position')
            ->pluck('shortcut_key')
            ->all();

        $keys = $this->buildSelection(
            primary: $keys,
            allowed: array_keys($catalog),
            fallbackSets: [
                (array) config('shortcuts.defaults_auth', []),
                (array) config('shortcuts.defaults_guest', []),
            ]
        );

        return $this->mapKeysToItems($keys, $catalog);
    }

    /**
     * @return array<int, array{key:string,label:string,url:string,requires_auth:bool}>
     */
    public function getAvailableForUser(?User $user): array
    {
        return array_values($this->resolvedCatalog(includeProtected: $user !== null));
    }

    /**
     * @param array<int, string> $keys
     * @return array<int, string>
     */
    public function saveForUser(User $user, array $keys): array
    {
        $catalog = $this->resolvedCatalog(includeProtected: true);
        $orderedKeys = $this->buildSelection(
            primary: $keys,
            allowed: array_keys($catalog),
            fallbackSets: [
                (array) config('shortcuts.defaults_auth', []),
                (array) config('shortcuts.defaults_guest', []),
            ]
        );

        DB::transaction(function () use ($user, $orderedKeys): void {
            UserShortcut::query()->where('user_id', $user->id)->delete();

            foreach ($orderedKeys as $index => $key) {
                UserShortcut::query()->create([
                    'user_id' => $user->id,
                    'shortcut_key' => $key,
                    'position' => $index + 1,
                ]);
            }
        });

        return $orderedKeys;
    }

    /**
     * @return array<int, string>
     */
    public function resetForUser(User $user): array
    {
        UserShortcut::query()->where('user_id', $user->id)->delete();

        return array_column($this->getForUser($user), 'key');
    }

    public function minShortcuts(): int
    {
        return max(1, (int) config('shortcuts.min', 1));
    }

    public function maxShortcuts(): int
    {
        return max($this->minShortcuts(), (int) config('shortcuts.max', 5));
    }

    /**
     * @return array<string, array{key:string,label:string,url:string,requires_auth:bool}>
     */
    private function resolvedCatalog(bool $includeProtected): array
    {
        $catalog = [];

        foreach ((array) config('shortcuts.catalog', []) as $key => $item) {
            $routeName = (string) ($item['route'] ?? '');
            if ($routeName === '' || ! Route::has($routeName)) {
                continue;
            }

            $requiresAuth = (bool) ($item['requires_auth'] ?? false);
            if (! $includeProtected && $requiresAuth) {
                continue;
            }

            $catalog[(string) $key] = [
                'key' => (string) $key,
                'label' => (string) ($item['label'] ?? (string) $key),
                'url' => route($routeName),
                'requires_auth' => $requiresAuth,
            ];
        }

        return $catalog;
    }

    /**
     * @param array<int, mixed> $primary
     * @param array<int, string> $allowed
     * @param array<int, array<int, mixed>> $fallbackSets
     * @return array<int, string>
     */
    private function buildSelection(array $primary, array $allowed, array $fallbackSets): array
    {
        $selected = $this->normalizeKeys($primary, $allowed);

        foreach ($fallbackSets as $fallbackSet) {
            if (count($selected) >= $this->minShortcuts()) {
                break;
            }

            foreach ($this->normalizeKeys($fallbackSet, $allowed) as $key) {
                if (! in_array($key, $selected, true)) {
                    $selected[] = $key;
                }

                if (count($selected) >= $this->maxShortcuts()) {
                    break 2;
                }
            }
        }

        if (count($selected) < $this->minShortcuts()) {
            foreach ($allowed as $key) {
                if (! in_array($key, $selected, true)) {
                    $selected[] = $key;
                }

                if (count($selected) >= $this->minShortcuts()) {
                    break;
                }
            }
        }

        return array_slice($selected, 0, $this->maxShortcuts());
    }

    /**
     * @param array<int, mixed> $keys
     * @param array<int, string> $allowed
     * @return array<int, string>
     */
    private function normalizeKeys(array $keys, array $allowed): array
    {
        $normalized = [];

        foreach ($keys as $key) {
            if (! is_scalar($key)) {
                continue;
            }

            $value = (string) $key;

            if (! in_array($value, $allowed, true)) {
                continue;
            }

            if (in_array($value, $normalized, true)) {
                continue;
            }

            $normalized[] = $value;

            if (count($normalized) >= $this->maxShortcuts()) {
                break;
            }
        }

        return $normalized;
    }

    /**
     * @param array<int, string> $keys
     * @param array<string, array{key:string,label:string,url:string,requires_auth:bool}> $catalog
     * @return array<int, array{key:string,label:string,url:string,requires_auth:bool}>
     */
    private function mapKeysToItems(array $keys, array $catalog): array
    {
        $items = [];
        foreach ($keys as $key) {
            if (isset($catalog[$key])) {
                $items[] = $catalog[$key];
            }
        }

        return $items;
    }
}
