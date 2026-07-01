<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    /**
     * Apakah user boleh melihat daftar penugasan.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('sekretariat');
    }

    /**
     * Apakah user boleh membuat penugasan baru.
     */
    public function create(User $user, \App\Models\Submission $submission): bool
    {
        return $user->hasRole('sekretariat') && $submission->secretary_id == $user->id;
    }

    /**
     * Apakah user boleh menghapus penugasan.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->hasRole('sekretariat') && $assignment->submission->secretary_id == $user->id;
    }
}
