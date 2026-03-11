<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AssistantResponse;
use RuntimeException;

class NullAssistantProvider implements AssistantProvider
{
    public function configured(): bool
    {
        return false;
    }

    public function name(): string
    {
        return 'none';
    }

    public function model(): string
    {
        return 'local-fallback';
    }

    public function generate(array $messages): AssistantResponse
    {
        throw new RuntimeException('Assistant provider is not configured.');
    }

    public function stream(array $messages, callable $onDelta): AssistantResponse
    {
        throw new RuntimeException('Assistant provider is not configured.');
    }
}
