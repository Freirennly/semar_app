<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewerAssigned extends Notification implements ShouldQueue
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
            ->subject('Penugasan Peninjauan Proposal - SEMAR')
            ->greeting('Halo Reviewer,')
            ->line('Anda telah ditugaskan untuk meninjau proposal berikut.')
            ->line('Judul: ' . $this->submission->title)
            ->action('Mulai Peninjauan', route('reviews.show', $this->submission))
            ->line('Terima kasih telah menggunakan aplikasi SEMAR.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Penugasan Reviewer Baru',
            'message' => 'Anda telah ditugaskan untuk meninjau proposal: "' . $this->submission->title . '"',
            'submission_id' => $this->submission->id,
            'url' => route('reviews.show', $this->submission),
            'action_url' => route('reviews.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
