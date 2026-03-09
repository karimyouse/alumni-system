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
        // ✅ نخزنها في DB (notifications table)
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        // ✅ نخليها متوافقة مع dropdown عندك (title/body/url)
        return [
            'kind'         => $this->payload['kind']         ?? 'content_review',
            'content_type' => $this->payload['content_type'] ?? null,
            'content_id'   => $this->payload['content_id']   ?? null,
            'status'       => $this->payload['status']       ?? null,

            'title'        => $this->payload['title']        ?? 'Content review',
            'body'         => $this->payload['body']         ?? ($this->payload['message'] ?? ''),
            'message'      => $this->payload['message']      ?? ($this->payload['body'] ?? ''),

            // dropdown عندك يقرأ url
            'url'          => $this->payload['url']          ?? null,

            'icon'         => $this->payload['icon']         ?? 'bell',
            'admin_note'   => $this->payload['admin_note']   ?? null,
        ];
    }
}
