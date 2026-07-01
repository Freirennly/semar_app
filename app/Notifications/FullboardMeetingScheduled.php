<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FullboardMeetingScheduled extends Notification
{
    use Queueable;

    protected $meeting;

    /**
     * Create a new notification instance.
     */
    public function __construct($meeting)
    {
        $this->meeting = $meeting;
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
    public function toArray(object $notifiable): array
    {
        $submission = $this->meeting->submission;
        
        $place = $this->meeting->location ?? $this->meeting->meeting_url ?? 'Tidak ada lokasi spesifik';
        $time = $this->meeting->scheduled_at->timezone('Asia/Jakarta')->format('d M Y, H:i');

        return [
            'title' => 'Rapat Fullboard Dijadwalkan',
            'message' => "Proposal \"{$submission->title}\" akan dibahas pada {$time}. Lokasi/Link: {$place}",
            'submission_id' => $submission->id,
            'action_url' => route('decisions.show', $submission->id),
        ];
    }
}
