<?php

namespace App\Services\AI;

class AssistantResponse
{
    /**
     * @param array<string, mixed> $metadata
     * @param array<string, int>|null $usage
     */
    public function __construct(
        public readonly string $content,
        public readonly string $provider,
        public readonly string $model,
        public readonly array $metadata = [],
        public readonly ?array $usage = null,
    ) {
    }
}
