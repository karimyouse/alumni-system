<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContentReviewNotification extends Notification
{
    use Queueable;

    public function __construct(public array $payload)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {

        return [
            'kind'         => $this->payload['kind']         ?? 'content_review',
            'content_type' => $this->payload['content_type'] ?? null,
            'content_id'   => $this->payload['content_id']   ?? null,
            'status'       => $this->payload['status']       ?? null,

            'title'        => $this->payload['title']        ?? 'Content review',
            'body'         => $this->payload['body']         ?? ($this->payload['message'] ?? ''),
            'message'      => $this->payload['message']      ?? ($this->payload['body'] ?? ''),


            'url'          => $this->payload['url']          ?? null,

            'icon'         => $this->payload['icon']         ?? 'bell',
            'admin_note'   => $this->payload['admin_note']   ?? null,
        ];
    }
}
