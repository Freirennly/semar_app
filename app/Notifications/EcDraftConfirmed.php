<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EcDraftConfirmed extends Notification
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
            'title' => 'Draf EC Dikonfirmasi',
            'message' => "Mahasiswa {$this->submission->student->name} telah mengonfirmasi Draf EC untuk proposal {$this->submission->code}.",
            'action_url' => route('admin.proposals.show', $this->submission),
            'icon' => 'check-circle'
        ];
    }
}
