<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EcDraftRevisionRequested extends Notification
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
            'title' => 'Permintaan Perbaikan Draf EC',
            'message' => "Mahasiswa {$this->submission->student->name} meminta perbaikan Draf EC untuk proposal {$this->submission->code}.",
            'action_url' => route('admin.proposals.show', $this->submission),
            'icon' => 'exclamation-circle'
        ];
    }
}
