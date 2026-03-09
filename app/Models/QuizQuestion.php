<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    public const TYPE_SINGLE_CHOICE = 'single_choice';
    public const TYPE_SHORT_TEXT = 'short_text';

    protected $fillable = [
        'quiz_id',
        'prompt',
        'question_type',
        'explanation',
        'accepted_answer',
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
            'accepted_answer' => 'string',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_SINGLE_CHOICE,
            self::TYPE_SHORT_TEXT,
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
