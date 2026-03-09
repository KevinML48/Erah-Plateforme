<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'label',
        'is_correct',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'question_id' => 'integer',
            'is_correct' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
