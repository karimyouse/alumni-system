<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountSuspended extends Notification
{
    use Queueable;

    public function __construct(
        public bool $isSuspended,
        public ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        if ($this->isSuspended) {
            return [
                'title' => 'Account suspended',
                'body'  => $this->reason
                    ? "Your account has been suspended. Reason: {$this->reason}"
                    : "Your account has been suspended by the system administrator. Please contact support.",
                'url'   => url('/login'),
            ];
        }

        return [
            'title' => 'Account re-activated',
            'body'  => 'Your account has been re-activated. You can sign in again.',
            'url'   => url('/login'),
        ];
    }
}
