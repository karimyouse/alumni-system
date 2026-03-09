<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyRegistrationSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        public string $companyName,
        public string $email,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New company registration',
            'body'  => "{$this->companyName} ({$this->email}) submitted a registration request.",
            'url'   => route('admin.companyApprovals', ['status' => 'pending']),
        ];
    }
}
