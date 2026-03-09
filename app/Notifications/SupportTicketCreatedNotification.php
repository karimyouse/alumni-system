<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupportTicketCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private array $payload)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->payload['title'] ?? 'New support ticket',
            'message' => $this->payload['message'] ?? '',
            'url' => $this->payload['url'] ?? null,
            'icon' => $this->payload['icon'] ?? 'help-circle',
            'ticket_id' => $this->payload['ticket_id'] ?? null,
            'kind' => 'support_ticket',
        ];
    }
}
