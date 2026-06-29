<?php

namespace App\Services;

/**
 * Service to handle notification dispatching (future implementation).
 *
 * Rules:
 * - Stateless service.
 * - Does not call other services.
 * - Does not send real notifications.
 */
class NotificationService
{
    /**
     * Send notification to a student related to a submission.
     *
     * Intent:
     * - Retrieve student associated with the submission.
     * - Determine notification template based on type.
     * - Send database and/or mail notification.
     *
     * @param int|string $submissionId
     * @param string $type
     * @return null
     */
    public function sendToStudent($submissionId, $type)
    {
        // Placeholder implementation
        return null;
    }

    /**
     * Send notification to a reviewer.
     *
     * Intent:
     * - Retrieve reviewer user.
     * - Determine notification template based on type.
     * - Send database and/or mail notification.
     *
     * @param int|string $reviewerId
     * @param string $type
     * @return null
     */
    public function sendToReviewer($reviewerId, $type)
    {
        // Placeholder implementation
        return null;
    }

    /**
     * Send notification based on a system event.
     *
     * Intent:
     * - Match the event key (e.g. 'submission.created', 'review.submitted').
     * - Resolve recipients and payload using the provided data.
     * - Dispatch notifications to corresponding users.
     *
     * @param string $event
     * @param array $data
     * @return null
     */
    public function sendByEvent($event, $data)
    {
        // Placeholder implementation
        return null;
    }
}
