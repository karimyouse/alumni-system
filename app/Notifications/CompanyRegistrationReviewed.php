<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyRegistrationReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,      
        public ?string $adminNote = null
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $title = $this->status === 'approved'
            ? 'Company account approved'
            : 'Company account rejected';

        $body = $this->status === 'approved'
            ? 'Your company account has been approved. You can now access your dashboard.'
            : ('Your company account has been rejected.' . ($this->adminNote ? " Reason: {$this->adminNote}" : ''));

        return [
            'title' => $title,
            'body'  => $body,
            'url'   => $this->status === 'approved' ? url('/company') : url('/login'),
        ];
    }
}
