<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactMessage extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSED = 'processused';
    public const STATUS_ARCHIVED = 'archived';

    public const CATEGORY_JOIN_CLUB = 'join_club';
    public const CATEGORY_PARTNERSHIP = 'partnership';
    public const CATEGORY_EVENT_LAN = 'event_lan';
    public const CATEGORY_STAGE_INTERVENTION = 'stage_intervention';
    public const CATEGORY_SUPPORT = 'support';
    public const CATEGORY_SUGGESTION = 'suggestion';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'category',
        'message',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_PROCESSED,
            self::STATUS_ARCHIVED,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_NEW => 'Nouveau',
            self::STATUS_PROCESSED => 'Traite',
            self::STATUS_ARCHIVED => 'Archive',
        ];
    }

    public function statusLabel(): string
    {
        $labels = self::statusLabels();

        return $labels[$this->status] ?? Str::headline((string) $this->status);
    }

    /**
     * @return array<int, string>
     */
    public static function categories(): array
    {
        return [
            self::CATEGORY_JOIN_CLUB,
            self::CATEGORY_PARTNERSHIP,
            self::CATEGORY_EVENT_LAN,
            self::CATEGORY_STAGE_INTERVENTION,
            self::CATEGORY_SUPPORT,
            self::CATEGORY_SUGGESTION,
            self::CATEGORY_OTHER,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_JOIN_CLUB => 'Rejoindre le club',
            self::CATEGORY_PARTNERSHIP => 'Partenariat',
            self::CATEGORY_EVENT_LAN => 'Evenement / LAN',
            self::CATEGORY_STAGE_INTERVENTION => 'Stage / intervention',
            self::CATEGORY_SUPPORT => 'Support / question',
            self::CATEGORY_SUGGESTION => 'Suggestion',
            self::CATEGORY_OTHER => 'Autre',
        ];
    }

    public function categoryLabel(): string
    {
        if (! filled($this->category)) {
            return 'Non précise';
        }

        $labels = self::categoryLabels();

        return $labels[(string) $this->category] ?? Str::headline((string) $this->category);
    }
}

