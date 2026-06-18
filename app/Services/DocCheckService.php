<?php

namespace App\Services;

/**
 * Service to handle document check workflows (Phase 3 Shadow Mode).
 */
class DocCheckService
{
    /**
     * Approve a document check submission.
     *
     * @param int|string $submissionId
     * @return array
     */
    public function approve($submissionId)
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

        // In shadow mode, the status might have already been updated to PROCESS.
        // So we consider it valid if the status is PROCESS, NEW_PROPOSAL, or REVISED.
        $validStatuses = [
            \App\Enums\SubmissionStatus::NEW_PROPOSAL,
            \App\Enums\SubmissionStatus::REVISED,
            \App\Enums\SubmissionStatus::PROCESS,
        ];

        $isValid = in_array($submission->status, $validStatuses);

        return [
            'status' => 'shadow_only',
            'valid' => $isValid,
            'reason' => $isValid ? null : 'Status tidak tepat untuk penyetujuan dokumen.',
            'computed_state' => [
                'submission_id' => $submissionId,
                'current_status' => $submission->status->value,
                'target_status' => \App\Enums\SubmissionStatus::PROCESS->value,
            ]
        ];
    }

    /**
     * Return a submission to draft with a note.
     *
     * @param int|string $submissionId
     * @param string $note
     * @return array
     */
    public function returnToDraft($submissionId, $note)
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

        // In shadow mode, legacy will transition to RESUBMISSION.
        // Valid previous statuses were NEW_PROPOSAL or REVISED.
        $validStatuses = [
            \App\Enums\SubmissionStatus::NEW_PROPOSAL,
            \App\Enums\SubmissionStatus::REVISED,
            \App\Enums\SubmissionStatus::RESUBMISSION,
        ];

        $isValid = in_array($submission->status, $validStatuses);
        $noteValid = ! empty($note) && is_string($note) && strlen($note) <= 2000;

        $valid = $isValid && $noteValid;
        $reason = null;
        if (! $isValid) {
            $reason = 'Status tidak tepat untuk pengembalian ke draft.';
        } elseif (! $noteValid) {
            $reason = 'Catatan revisi tidak valid.';
        }

        return [
            'status' => 'shadow_only',
            'valid' => $valid,
            'reason' => $reason,
            'computed_state' => [
                'submission_id' => $submissionId,
                'current_status' => $submission->status->value,
                'target_status' => \App\Enums\SubmissionStatus::RESUBMISSION->value,
                'note' => $note,
            ]
        ];
    }

    /**
     * Validate the uploaded documents for a submission.
     *
     * @param int|string $submissionId
     * @return array
     */
    public function validateDocuments($submissionId)
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
