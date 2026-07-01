<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalAssignedToSecretary extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $submission)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Penugasan Sekretariat Baru',
            'message' => "Anda telah ditugaskan sebagai sekretariat untuk proposal: {$this->submission->title}",
            'action_url' => route('doccheck.show', $this->submission),
            'icon' => 'clipboard-document-list',
        ];
    }
}
