<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistantMessage extends Model
{
    public const ROLE_USER = 'user';
    public const ROLE_ASSISTANT = 'assistant';
    public const ROLE_SYSTEM = 'system';

    protected $fillable = [
        'assistant_conversation_id',
        'role',
        'content',
        'provider',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'assistant_conversation_id' => 'integer',
            'prompt_tokens' => 'integer',
            'completion_tokens' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AssistantConversation::class, 'assistant_conversation_id');
    }
}
