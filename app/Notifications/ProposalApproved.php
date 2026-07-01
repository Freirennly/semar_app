<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalApproved extends Notification implements ShouldQueue
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
            ->subject('Proposal Disetujui (APPROVED) - SEMAR')
            ->greeting('Halo Mahasiswa,')
            ->line('Selamat! Proposal Anda telah disetujui (APPROVED) oleh Sekretariat.')
            ->line('Judul: ' . $this->submission->title)
            ->action('Lihat Detail', route('submissions.show', $this->submission))
            ->line('Silakan pantau halaman detail untuk menunggu draf Ethical Clearance Anda dibuat.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Proposal Disetujui',
            'message' => 'Status proposal Anda "' . $this->submission->title . '" telah diperbarui menjadi APPROVED.',
            'submission_id' => $this->submission->id,
            'url' => route('submissions.show', $this->submission),
            'action_url' => route('submissions.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
