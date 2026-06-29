<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubmissionWorkflowNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $message;
    protected int $submissionId;
    protected ?string $actionUrl;

    public function __construct(string $title, string $message, int $submissionId, ?string $actionUrl = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->submissionId = $submissionId;
        $this->actionUrl = $actionUrl;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'submission_id' => $this->submissionId,
            'action_url' => $this->actionUrl,
        ];
    }
}
