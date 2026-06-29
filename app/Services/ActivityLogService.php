<?php

namespace App\Services;

/**
 * Service to handle activity logging concepts (future implementation).
 *
 * Rules:
 * - Does not write to database in this phase.
 */
class ActivityLogService
{
    /**
     * Log a submission status change activity.
     *
     * Intent:
     * - Create a new ActivityLog entry.
     * - Record the actor, submission ID, old status, and new status.
     * - Include any note or description describing the change.
     *
     * @param int|string $submissionId
     * @param string $status
     * @return null
     */
    public function logStatusChange($submissionId, $status)
    {
        // Placeholder implementation
        return null;
    }

    /**
     * Log a reviewer assignment activity.
     *
     * Intent:
     * - Create a new ActivityLog entry.
     * - Record that reviewerId has been assigned to submissionId.
     *
     * @param int|string $submissionId
     * @param int|string $reviewerId
     * @return null
     */
    public function logAssignment($submissionId, $reviewerId)
    {
        // Placeholder implementation
        return null;
    }
}
