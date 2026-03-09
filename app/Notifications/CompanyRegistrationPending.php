<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyRegistrationPending extends Notification
{
    use Queueable;

    public function __construct(
        public string $companyName,
        public string $email
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'New company registration',
            'body'  => "{$this->companyName} ({$this->email}) requested access. Review approvals.",
            'type'  => 'company_registration',
        ];
    }
}
