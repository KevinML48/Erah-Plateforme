<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClipVote extends Model
{
    protected $fillable = [
        'campaign_id',
        'clip_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'campaign_id' => 'integer',
            'clip_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(ClipVoteCampaign::class, 'campaign_id');
    }

    public function clip(): BelongsTo
    {
        return $this->belongsTo(Clip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
