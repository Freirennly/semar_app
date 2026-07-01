<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProposalSubmitted extends Notification implements ShouldQueue
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
            ->subject('Proposal Baru Diajukan - SEMAR')
            ->greeting('Halo Admin / Sekretariat,')
            ->line('Mahasiswa ' . $this->submission->student->name . ' telah mengajukan proposal baru.')
            ->line('Judul: ' . $this->submission->title)
            ->action('Lihat Proposal', route('submissions.show', $this->submission))
            ->line('Terima kasih telah menggunakan aplikasi SEMAR.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Proposal Baru Diajukan',
            'message' => 'Mahasiswa ' . $this->submission->student->name . ' telah mengajukan proposal baru: "' . $this->submission->title . '"',
            'submission_id' => $this->submission->id,
            'url' => route('submissions.show', $this->submission),
            'action_url' => route('submissions.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
