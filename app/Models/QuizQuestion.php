<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'prompt',
        'explanation',
        'sort_order',
        'points',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quiz_id' => 'integer',
            'sort_order' => 'integer',
            'points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id')->orderBy('sort_order');
    }
}
