<?php

namespace App\Services\AI;

class AssistantPromptBuilder
{
    /**
     * @param array<string, mixed> $context
     */
    public function build(array $context): string
    {
        $basePrompt = (string) config('assistant.system_prompt');
        $dateLine = 'Date de référence: '.now()->toDateString().'.';

        return trim($basePrompt)."\n\n".$dateLine."\n\nContexte fiable ERAH:\n".$this->encodeContext($context);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function encodeContext(array $context): string
    {
        return (string) json_encode(
            $context,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
