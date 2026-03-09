<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSupportTicketNotification extends Notification
{
    use Queueable;

    public function __construct(public array $payload = [])
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->payload['title'] ?? 'Notification',
            'body'  => $this->payload['body'] ?? '',
            'url'   => $this->payload['url'] ?? null,
            'icon'  => $this->payload['icon'] ?? 'bell',
            'ticket_id' => $this->payload['ticket_id'] ?? null,
        ];
    }
}
