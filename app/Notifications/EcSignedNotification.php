<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EcSignedNotification extends Notification
{
    use Queueable;

    protected $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Ethical Clearance Diterbitkan',
            'message' => "Ethical Clearance untuk proposal {$this->submission->code} telah disahkan dan siap diunduh.",
            'action_url' => route('submissions.show', $this->submission),
            'icon' => 'check-circle'
        ];
    }
}
