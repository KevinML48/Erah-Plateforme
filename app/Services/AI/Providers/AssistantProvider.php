<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AssistantResponse;

interface AssistantProvider
{
    public function configured(): bool;

    public function name(): string;

    public function model(): string;

    /**
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function generate(array $messages): AssistantResponse;

    /**
     * @param array<int, array{role: string, content: string}> $messages
     * @param callable(string): void $onDelta
     */
    public function stream(array $messages, callable $onDelta): AssistantResponse;
}
