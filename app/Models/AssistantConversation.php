<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssistantConversation extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'provider',
        'model',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'last_message_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AssistantMessage::class)->orderBy('id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(AssistantMessage::class)->latestOfMany();
    }
}
