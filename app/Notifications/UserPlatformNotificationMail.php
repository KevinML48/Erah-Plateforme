<?php

namespace App\Notifications;

use App\Models\Notification as PlatformNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UserPlatformNotificationMail extends Notification
{
    use Queueable;

    public function __construct(
        private readonly PlatformNotification $platformNotification
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject())
            ->greeting('Bonjour '.trim((string) ($notifiable->name ?? 'membre')).',')
            ->line($this->platformNotification->message)
            ->line('Categorie: '.$this->categoryLabel())
            ->action('Ouvrir mes notifications', route('app.notifications.index'))
            ->line('Vous recevez cet email car les notifications email sont actives sur votre compte ERAH.');

        foreach ($this->detailLines() as $line) {
            $mail->line($line);
        }

        return $mail;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'notification_id' => $this->platformNotification->id,
            'category' => $this->platformNotification->category,
            'title' => $this->platformNotification->title,
            'message' => $this->platformNotification->message,
            'data' => $this->platformNotification->data,
        ];
    }

    private function subject(): string
    {
        $title = trim((string) ($this->platformNotification->title ?? ''));

        if ($title !== '') {
            return '[ERAH] '.$title;
        }

        return '[ERAH] Nouvelle notification';
    }

    private function categoryLabel(): string
    {
        return Str::headline((string) $this->platformNotification->category);
    }

    /**
     * @return array<int, string>
     */
    private function detailLines(): array
    {
        $data = is_array($this->platformNotification->data) ? $this->platformNotification->data : [];
        $lines = [];

        $reason = trim((string) Arr::get($data, 'reason', ''));
        if ($reason !== '') {
            $lines[] = 'Motif: '.$reason;
        }

        $trackingCarrier = trim((string) Arr::get($data, 'tracking_carrier', ''));
        $trackingCode = trim((string) Arr::get($data, 'tracking_code', ''));
        if ($trackingCarrier !== '' || $trackingCode !== '') {
            $tracking = 'Suivi';

            if ($trackingCarrier !== '') {
                $tracking .= ' '.$trackingCarrier;
            }

            if ($trackingCode !== '') {
                $tracking .= ': '.$trackingCode;
            }

            $lines[] = $tracking;
        }

        $rewardMonth = trim((string) Arr::get($data, 'reward_month', ''));
        if ($rewardMonth !== '') {
            $lines[] = 'Periode: '.$rewardMonth;
        }

        return $lines;
    }
}