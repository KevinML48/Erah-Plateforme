<?php

namespace App\Http\Requests\App;

use App\Services\ShortcutService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShortcutsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var ShortcutService $shortcutService */
        $shortcutService = app(ShortcutService::class);
        $allowedKeys = array_column($shortcutService->getAvailableForUser($this->user()), 'key');
        $max = $shortcutService->maxShortcuts();
        $min = $shortcutService->minShortcuts();

        return [
            'shortcuts' => ['required', 'array', 'min:'.$min, 'max:'.$max],
            'shortcuts.*' => ['required', 'string', 'distinct', Rule::in($allowedKeys)],
            'orders' => ['nullable', 'array'],
            'orders.*' => ['nullable', 'integer', 'min:1', 'max:'.$max],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function orderedShortcutKeys(): array
    {
        /** @var array<int, string> $keys */
        $keys = $this->validated('shortcuts', []);
        /** @var array<string, int|string|null> $orders */
        $orders = $this->validated('orders', []);
        $originalOrder = array_values($keys);

        usort($keys, function (string $a, string $b) use ($orders, $originalOrder): int {
            $orderA = isset($orders[$a]) ? (int) $orders[$a] : 999;
            $orderB = isset($orders[$b]) ? (int) $orders[$b] : 999;

            if ($orderA !== $orderB) {
                return $orderA <=> $orderB;
            }

            return array_search($a, $originalOrder, true) <=> array_search($b, $originalOrder, true);
        });

        return array_values(array_unique($keys));
    }
}

