<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BanNotification extends Notification
{
    use Queueable;

    protected $reason;
    protected $until;

    public function __construct($reason, $until)
    {
        $this->reason = $reason;
        $this->until = $until;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reason' => $this->reason,
            'until' => $this->until ? $this->until->format('Y-m-d') : 'Permanent',
        ];
    }
};