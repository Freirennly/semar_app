<?php

namespace App\Services;

/**
 * Service to handle review workflows (Phase 3 Shadow Mode).
 */
class ReviewService
{
    /**
     * Submit a review for an assignment.
     *
     * @param int|string $assignmentId
     * @param array $data
     * @return array
     */
    public function submitReview($assignmentId, $data)
    {
        if (! config('semar.phase3_shadow', true)) {
            return [
                'status' => 'disabled',
                'valid' => false,
                'reason' => 'Shadow mode is disabled.',
                'computed_state' => [],
            ];
        }

        $assignment = \App\Models\Assignment::find($assignmentId);
        if (! $assignment) {
            return [
                'status' => 'shadow_only',
                'valid' => false,
                'reason' => 'Assignment not found.',
                'computed_state' => [],
            ];
        }

        $user = auth()->user();
        if (! $user) {
            return [
                'status' => 'shadow_only',
                'valid' => false,
                'reason' => 'Unauthenticated user.',
                'computed_state' => [],
            ];
        }

        // Validate reviewer ownership
        $isOwner = ($assignment->reviewer_id === $user->id);
        if (! $isOwner) {
            return [
                'status' => 'shadow_only',
                'valid' => false,
                'reason' => 'User is not the assigned reviewer for this assignment.',
                'computed_state' => [],
            ];
        }

        // Validate completion logic
        $recommendationValid = isset($data['recommendation']) && in_array($data['recommendation'], ['APPROVE', 'REVISION', 'REJECT']);
        $notesValid = isset($data['notes']) && is_string($data['notes']) && strlen($data['notes']) <= 10000;

        $valid = $recommendationValid && $notesValid;
        $reason = null;
        if (! $recommendationValid) {
            $reason = 'Rekomendasi tidak valid.';
        } elseif (! $notesValid) {
            $reason = 'Catatan review tidak valid.';
        }

        return [
            'status' => 'shadow_only',
            'valid' => $valid,
            'reason' => $reason,
            'computed_state' => [
                'assignment_id' => $assignmentId,
                'reviewer_id' => $user->id,
                'submission_id' => $assignment->submission_id,
                'recommendation' => $data['recommendation'] ?? null,
                'notes' => $data['notes'] ?? null,
                'simulated_assignment_status' => 'COMPLETED',
            ]
        ];
    }
}
