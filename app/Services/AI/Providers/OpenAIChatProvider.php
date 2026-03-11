<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AssistantResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class OpenAIChatProvider implements AssistantProvider
{
    public function configured(): bool
    {
        return filled($this->apiKey()) && $this->name() === 'openai';
    }

    public function name(): string
    {
        return (string) config('assistant.provider', 'none');
    }

    public function model(): string
    {
        return (string) config('assistant.model', 'gpt-4.1-mini');
    }

    public function generate(array $messages): AssistantResponse
    {
        $response = Http::baseUrl($this->baseUrl())
            ->withToken($this->apiKey())
            ->acceptJson()
            ->timeout($this->timeout())
            ->post('/chat/completions', $this->payload($messages));

        try {
            $response->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException('Unable to reach the assistant provider.', previous: $exception);
        }

        $json = $response->json();
        $content = $this->extractContent(data_get($json, 'choices.0.message.content'));

        if ($content === '') {
            throw new RuntimeException('Assistant provider returned an empty response.');
        }

        return new AssistantResponse(
            content: $content,
            provider: 'openai',
            model: (string) (data_get($json, 'model') ?: $this->model()),
            usage: [
                'prompt_tokens' => (int) data_get($json, 'usage.prompt_tokens', 0),
                'completion_tokens' => (int) data_get($json, 'usage.completion_tokens', 0),
            ],
        );
    }

    public function stream(array $messages, callable $onDelta): AssistantResponse
    {
        $response = Http::baseUrl($this->baseUrl())
            ->withToken($this->apiKey())
            ->accept('text/event-stream')
            ->timeout($this->timeout())
            ->withOptions(['stream' => true])
            ->send('POST', '/chat/completions', [
                'json' => $this->payload($messages, true),
            ]);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException('Unable to stream from the assistant provider.', previous: $exception);
        }

        $stream = $response->toPsrResponse()->getBody();
        $buffer = '';
        $content = '';
        $usage = null;

        while (! $stream->eof()) {
            $buffer .= $stream->read(2048);

            while (($separator = strpos($buffer, "\n\n")) !== false) {
                $rawEvent = substr($buffer, 0, $separator);
                $buffer = substr($buffer, $separator + 2);
                $payload = $this->parseSsePayload($rawEvent);

                if ($payload === null || $payload === '') {
                    continue;
                }

                if ($payload === '[DONE]') {
                    break 2;
                }

                try {
                    $json = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
                } catch (Throwable) {
                    continue;
                }

                $delta = $this->extractContent(data_get($json, 'choices.0.delta.content'));

                if ($delta !== '') {
                    $content .= $delta;
                    $onDelta($delta);
                }

                if (is_array(data_get($json, 'usage'))) {
                    $usage = [
                        'prompt_tokens' => (int) data_get($json, 'usage.prompt_tokens', 0),
                        'completion_tokens' => (int) data_get($json, 'usage.completion_tokens', 0),
                    ];
                }
            }
        }

        $content = trim($content);

        if ($content === '') {
            throw new RuntimeException('Assistant provider stream returned an empty response.');
        }

        return new AssistantResponse(
            content: $content,
            provider: 'openai',
            model: $this->model(),
            usage: $usage,
        );
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     * @return array<string, mixed>
     */
    private function payload(array $messages, bool $stream = false): array
    {
        $payload = [
            'model' => $this->model(),
            'messages' => $messages,
            'temperature' => (float) config('assistant.temperature', 0.45),
            'max_tokens' => (int) config('assistant.max_tokens', 900),
        ];

        if ($stream) {
            $payload['stream'] = true;
            $payload['stream_options'] = ['include_usage' => true];
        }

        return $payload;
    }

    private function baseUrl(): string
    {
        return (string) config('assistant.base_url', 'https://api.openai.com/v1');
    }

    private function apiKey(): ?string
    {
        return config('assistant.api_key');
    }

    private function timeout(): int
    {
        return (int) config('assistant.timeout', 45);
    }

    private function parseSsePayload(string $rawEvent): ?string
    {
        $lines = preg_split("/\r\n|\n|\r/", trim($rawEvent)) ?: [];
        $data = [];

        foreach ($lines as $line) {
            if (! str_starts_with($line, 'data:')) {
                continue;
            }

            $data[] = trim(substr($line, 5));
        }

        if ($data === []) {
            return null;
        }

        return implode("\n", $data);
    }

    private function extractContent(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (! is_array($value)) {
            return '';
        }

        $parts = [];

        foreach ($value as $item) {
            if (is_string($item)) {
                $parts[] = $item;
                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            if (is_string($item['text'] ?? null)) {
                $parts[] = $item['text'];
                continue;
            }

            if (is_string($item['content'] ?? null)) {
                $parts[] = $item['content'];
            }
        }

        return implode('', $parts);
    }
}
