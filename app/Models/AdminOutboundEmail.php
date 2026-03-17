<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AdminOutboundEmail extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    public const CATEGORY_SUPPORT = 'support';
    public const CATEGORY_ACCOUNT = 'account';
    public const CATEGORY_REWARD = 'reward';
    public const CATEGORY_SUPPORTER = 'supporter';
    public const CATEGORY_MODERATION = 'moderation';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'uuid',
        'sender_admin_user_id',
        'recipient_user_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'body_html',
        'body_text',
        'category',
        'status',
        'mailer',
        'provider',
        'provider_message_id',
        'queued_at',
        'sent_at',
        'failed_at',
        'failure_reason',
        'meta',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $email): void {
            if (! filled($email->uuid)) {
                $email->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'sender_admin_user_id' => 'integer',
            'recipient_user_id' => 'integer',
            'queued_at' => 'datetime',
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_QUEUED,
            self::STATUS_SENT,
            self::STATUS_FAILED,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_QUEUED => 'En queue',
            self::STATUS_SENT => 'Envoye',
            self::STATUS_FAILED => 'Echec',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? Str::headline((string) $this->status);
    }

    /**
     * @return array<int, string>
     */
    public static function categories(): array
    {
        return [
            self::CATEGORY_SUPPORT,
            self::CATEGORY_ACCOUNT,
            self::CATEGORY_REWARD,
            self::CATEGORY_SUPPORTER,
            self::CATEGORY_MODERATION,
            self::CATEGORY_OTHER,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_SUPPORT => 'Support',
            self::CATEGORY_ACCOUNT => 'Information compte',
            self::CATEGORY_REWARD => 'Recompense / cadeau',
            self::CATEGORY_SUPPORTER => 'Supporter',
            self::CATEGORY_MODERATION => 'Moderation',
            self::CATEGORY_OTHER => 'Autre',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categoryLabels()[$this->category] ?? Str::headline((string) $this->category);
    }

    public function senderAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_admin_user_id');
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}