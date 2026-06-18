<?php

namespace App\Services;

/**
 * Service to handle assignment workflows (Phase 3 Shadow Mode).
 */
class AssignmentService
{
    /**
     * Assign reviewer(s) to a submission.
     *
     * @param int|string $submissionId
     * @param array|int|string $reviewerIds
     * @param string|null $dueDate
     * @param string|null $category
     * @return array
     */
    public function assign($submissionId, $reviewerIds, $dueDate, $category)
    {
        if (! config('semar.phase3_shadow', true)) {
            return [
                'status' => 'disabled',
                'valid' => false,
                'reason' => 'Shadow mode is disabled.',
                'computed_state' => [],
            ];
        }

        $submission = \App\Models\Submission::find($submissionId);
        if (! $submission) {
            return [
                'status' => 'shadow_only',
                'valid' => false,
                'reason' => 'Submission not found.',
                'computed_state' => [],
            ];
        }

        $ids = is_array($reviewerIds) ? $reviewerIds : [$reviewerIds];
        $valid = true;
        $reason = null;
        $computedState = [
            'submission_id' => $submissionId,
            'reviewers' => [],
            'due_date' => $dueDate,
            'category' => $category,
        ];

        foreach ($ids as $reviewerId) {
            $reviewer = \App\Models\User::find($reviewerId);
            if (! $reviewer) {
                $valid = false;
                $reason = "User ID {$reviewerId} not found.";
                break;
            }

            if (! $reviewer->hasRole('reviewer')) {
                $valid = false;
                $reason = "User {$reviewer->name} yang dipilih bukan reviewer.";
                break;
            }

            // Note: In shadow mode, since legacy runs first, the assignment might have already been created.
            // If the count is > 1 for this reviewer, or if it already existed before, it might be a duplicate.
            $count = $submission->assignments()->where('reviewer_id', $reviewer->id)->count();
            if ($count > 1) {
                $valid = false;
                $reason = "Reviewer {$reviewer->name} sudah ditugaskan lebih dari sekali.";
                break;
            }

            $computedState['reviewers'][] = [
                'id' => $reviewer->id,
                'name' => $reviewer->name,
            ];
        }

        return [
            'status' => 'shadow_only',
            'valid' => $valid,
            'reason' => $reason,
            'computed_state' => $computedState,
        ];
    }

    /**
     * Remove an assignment by its ID.
     *
     * @param int|string $assignmentId
     * @param int|string|null $submissionId
     * @return array
     */
    public function unassign($assignmentId, $submissionId = null)
    {
        if (! config('semar.phase3_shadow', true)) {
            return [
                'status' => 'disabled',
                'valid' => false,
                'reason' => 'Shadow mode is disabled.',
                'computed_state' => [],
            ];
        }

        // Under shadow mode, the assignment is already deleted by legacy.
        $assignment = \App\Models\Assignment::find($assignmentId);
        
        $valid = true;
        $reason = null;

        // If the assignment is still in DB, we would simulate deletion.
        $deletedSuccessfully = ($assignment === null);

        return [
            'status' => 'shadow_only',
            'valid' => $valid,
            'reason' => $reason,
            'computed_state' => [
                'assignment_id' => $assignmentId,
                'submission_id' => $submissionId ?? ($assignment ? $assignment->submission_id : null),
                'deleted_by_legacy' => $deletedSuccessfully,
            ]
        ];
    }

    /**
     * Sync the status of a submission based on its assignments.
     *
     * @param int|string $submissionId
     * @return array
     */
    public function syncStatus($submissionId)
    {
        if (! config('semar.phase3_shadow', true)) {
            return [
                'status' => 'disabled',
                'valid' => false,
                'reason' => 'Shadow mode is disabled.',
                'computed_state' => [],
            ];
        }

        $submission = \App\Models\Submission::find($submissionId);
        $valid = $submission !== null;

        return [
            'status' => 'shadow_only',
            'valid' => $valid,
            'reason' => $valid ? null : 'Submission not found.',
            'computed_state' => [
                'submission_id' => $submissionId,
            ]
        ];
    }
}
