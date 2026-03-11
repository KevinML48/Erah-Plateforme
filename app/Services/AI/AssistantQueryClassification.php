<?php

namespace App\Services\AI;

class AssistantQueryClassification
{
    /**
     * @param array<int, string> $tokens
     * @param array<int, string> $matchedTopics
     */
    public function __construct(
        public readonly string $kind,
        public readonly float $confidence,
        public readonly string $normalized,
        public readonly array $tokens = [],
        public readonly array $matchedTopics = [],
        public readonly ?string $fallbackMessage = null,
        public readonly ?string $reason = null,
    ) {
    }

    public function isClear(): bool
    {
        return $this->kind === 'clear';
    }

    public function needsClarification(): bool
    {
        return $this->kind === 'needs_clarification';
    }

    public function isOutOfScope(): bool
    {
        return $this->kind === 'out_of_scope';
    }

    public function requiresGuardResponse(): bool
    {
        return ! $this->isClear();
    }
}
