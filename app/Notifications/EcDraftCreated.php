<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EcDraftCreated extends Notification implements ShouldQueue
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
            ->subject('Draf Ethical Clearance Dibuat - SEMAR')
            ->greeting('Halo Mahasiswa,')
            ->line('Draf sertifikat Ethical Clearance untuk proposal Anda telah dibuat.')
            ->line('Judul: ' . $this->submission->title)
            ->action('Konfirmasi Data', route('submissions.show', $this->submission))
            ->line('Silakan periksa dan konfirmasikan judul penelitian serta nama peneliti agar dapat diajukan ke Ketua.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Draf Ethical Clearance Dibuat',
            'message' => 'Draf sertifikat Ethical Clearance untuk proposal "' . $this->submission->title . '" telah dibuat. Silakan lakukan konfirmasi.',
            'submission_id' => $this->submission->id,
            'url' => route('submissions.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
