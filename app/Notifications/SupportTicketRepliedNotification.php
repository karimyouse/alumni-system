<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupportTicketRepliedNotification extends Notification
{
    use Queueable;

    public function __construct(public array $payload = [])
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind'       => $this->payload['kind'] ?? 'support_ticket_reply',
            'title'      => $this->payload['title'] ?? 'Support Reply',
            'body'       => $this->payload['body'] ?? ($this->payload['message'] ?? ''),
            'message'    => $this->payload['message'] ?? ($this->payload['body'] ?? ''),
            'url'        => $this->payload['url'] ?? null,
            'icon'       => $this->payload['icon'] ?? 'message-square',
            'ticket_id'  => $this->payload['ticket_id'] ?? null,
            'status'     => $this->payload['status'] ?? null,
            'created_at' => $this->payload['created_at'] ?? now()->toISOString(),
        ];
    }
}
