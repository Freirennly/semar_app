<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EcWaitingSignature extends Notification implements ShouldQueue
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
            ->subject('Tanda Tangan Sertifikat Laik Etik - SEMAR')
            ->greeting('Halo Ketua,')
            ->line('Sertifikat Ethical Clearance untuk proposal berikut siap untuk Anda tanda tangani.')
            ->line('Judul: ' . $this->submission->confirmed_title ?: $this->submission->title)
            ->line('Peneliti: ' . $this->submission->confirmed_researcher_name)
            ->action('Tinjau & Tanda Tangan', route('submissions.show', $this->submission))
            ->line('Terima kasih atas kerja samanya.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Menunggu Tanda Tangan Sertifikat',
            'message' => 'Sertifikat untuk proposal "' . ($this->submission->confirmed_title ?: $this->submission->title) . '" menunggu tanda tangan Anda.',
            'submission_id' => $this->submission->id,
            'url' => route('submissions.show', $this->submission),
            'action_url' => route('submissions.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
