<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalRevisionRequested extends Notification implements ShouldQueue
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
            ->subject('Permintaan Revisi Proposal - SEMAR')
            ->greeting('Halo Mahasiswa,')
            ->line('Sekretariat meminta Anda melakukan revisi pada proposal berikut.')
            ->line('Judul: ' . $this->submission->title)
            ->action('Unggah Revisi', route('submissions.show', $this->submission))
            ->line('Harap segera mengunggah berkas revisi agar proses peninjauan dapat dilanjutkan.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Permintaan Revisi Proposal',
            'message' => 'Anda diminta untuk melakukan revisi pada proposal: "' . $this->submission->title . '"',
            'submission_id' => $this->submission->id,
            'url' => route('submissions.show', $this->submission),
            'action_url' => route('submissions.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
