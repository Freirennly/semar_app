<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EcCertificateIssued extends Notification implements ShouldQueue
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
            ->subject('Sertifikat Laik Etik Terbit (DONE) - SEMAR')
            ->greeting('Halo Mahasiswa,')
            ->line('Selamat! Sertifikat Laik Etik untuk proposal Anda telah resmi diterbitkan.')
            ->line('Judul: ' . $this->submission->confirmed_title ?: $this->submission->title)
            ->action('Unduh Sertifikat', route('submissions.show', $this->submission))
            ->line('Terima kasih telah menggunakan sistem SEMAR untuk pengurusan kelaikan etik penelitian Anda.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Sertifikat Laik Etik Terbit',
            'message' => 'Selamat! Sertifikat Laik Etik untuk proposal "' . ($this->submission->confirmed_title ?: $this->submission->title) . '" telah selesai diterbitkan dan dapat diunduh.',
            'submission_id' => $this->submission->id,
            'url' => route('submissions.show', $this->submission),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
