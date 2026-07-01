<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\Submission;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Apakah user boleh melihat daftar review miliknya.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('reviewer');
    }

    /**
     * Apakah user (reviewer yang ditugaskan) boleh melihat detail review.
     */
    public function view(User $user, Submission $submission): bool
    {
        return $user->hasRole('reviewer')
            && $submission->assignments()->where('reviewer_id', $user->id)->exists();
    }

    /**
     * Apakah reviewer boleh submit review.
     */
    public function create(User $user, Submission $submission): bool
    {
        return $user->hasRole('reviewer')
            && $submission->assignments()->where('reviewer_id', $user->id)->exists();
    }
}
