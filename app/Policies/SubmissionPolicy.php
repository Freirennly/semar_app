<?php

namespace App\Policies;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('submission.create');
    }

    public function view(User $user, Submission $submission): bool
    {
        if ($user->hasPermissionTo('submission.view_all')) return true;
        if ($user->hasPermissionTo('submission.view_own') && $submission->student_id === $user->id) return true;
        if ($user->hasPermissionTo('submission.view_assigned')) {
            return $submission->assignments()->where('reviewer_id', $user->id)->exists();
        }
        return false;
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->hasPermissionTo('submission.update_own_draft')
            && $submission->student_id === $user->id
            && in_array($submission->status, [SubmissionStatus::DRAFT, SubmissionStatus::RESUBMISSION]);
    }
}
