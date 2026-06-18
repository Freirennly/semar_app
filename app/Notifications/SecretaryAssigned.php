<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecretaryAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected Submission $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
        $this->queue = 'notifications';
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Penugasan Sekretaris - SEMAR')
            ->greeting('Halo Sekretariat,')
            ->line('Anda telah ditugaskan sebagai sekretaris untuk mengelola proposal berikut.')
            ->line('Judul: ' . $this->submission->title)
            ->action('Kelola Proposal', route('submissions.show', $this->submission))
            ->line('Terima kasih telah menggunakan aplikasi SEMAR.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Penugasan Sekretaris',
            'message' => 'Anda telah ditugaskan sebagai sekretaris untuk proposal: "' . $this->submission->title . '"',
            'submission_id' => $this->submission->id,
            'url' => route('submissions.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
