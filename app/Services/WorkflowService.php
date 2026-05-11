<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\StatusHistory;
use App\Models\User;

class WorkflowService
{
    private const TRANSITIONS = [
        'DRAFT' => ['SUBMITTED'],
        'SUBMITTED' => ['DOC_CHECK', 'DRAFT'],
        'DOC_CHECK' => ['DRAFT', 'ASSIGNED'],
        'ASSIGNED' => ['UNDER_REVIEW'],
        'UNDER_REVIEW' => ['PENDING_DECISION'],
        'PENDING_DECISION' => ['APPROVED', 'RESUBMISSION', 'DISAPPROVED'],
        'APPROVED' => ['ARCHIVED'],
        'RESUBMISSION' => ['SUBMITTED'],
        'DISAPPROVED' => ['ARCHIVED'],
    ];

    public function canTransition(Submission $submission, SubmissionStatus $newStatus): bool
    {
        $current = $submission->status->value;
        return in_array($newStatus->value, self::TRANSITIONS[$current] ?? []);
    }

    public function transition(Submission $submission, SubmissionStatus $newStatus, User $actor, ?string $note = null): void
    {
        if (! $this->canTransition($submission, $newStatus)) {
            abort(403, "Transisi dari {$submission->status->value} ke {$newStatus->value} tidak diizinkan.");
        }

        $oldStatus = $submission->status->value;
        $submission->update(['status' => $newStatus]);

        StatusHistory::create([
            'submission_id' => $submission->id,
            'from_status' => $oldStatus,
            'to_status' => $newStatus->value,
            'changed_by' => $actor->id,
            'note' => $note,
            'created_at' => now(),
        ]);
    }
}
