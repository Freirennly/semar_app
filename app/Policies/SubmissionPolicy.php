<?php

namespace App\Policies;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Apakah user boleh membuat pengajuan baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('submission.create');
    }

    /**
     * Apakah user boleh melihat detail pengajuan.
     */
    public function view(User $user, Submission $submission): bool
    {
        if ($user->hasRole('admin')) return true;
        
        if ($user->hasPermissionTo('submission.view_all')) {
            if ($user->hasRole('sekretariat')) {
                return $submission->secretary_id == $user->id;
            }
            return true;
        }
        if ($user->hasPermissionTo('submission.view_own') && $submission->student_id === $user->id) return true;
        if ($user->hasPermissionTo('submission.view_assigned')) {
            return $submission->assignments()->where('reviewer_id', $user->id)->exists();
        }
        return false;
    }

    /**
     * Apakah user boleh mengedit pengajuan.
     */
    public function update(User $user, Submission $submission): bool
    {
        return $user->hasPermissionTo('submission.update_own_draft')
            && $submission->student_id === $user->id
            && $submission->status === SubmissionStatus::REVISION_REQUIRED;
    }

    /**
     * Apakah student pemilik boleh mengirim revisi.
     */
    public function submit(User $user, Submission $submission): bool
    {
        return $user->hasRole('student')
            && $submission->student_id === $user->id;
    }

    /**
     * Apakah student pemilik boleh mengupload dokumen.
     */
    public function uploadDocument(User $user, Submission $submission): bool
    {
        return $user->hasRole('student')
            && $submission->student_id === $user->id;
    }

    /**
     * Apakah student pemilik boleh menghapus dokumen pada pengajuannya.
     */
    public function deleteDocument(User $user, Submission $submission): bool
    {
        return $user->hasRole('student')
            && $submission->student_id === $user->id;
    }

    /**
     * Apakah user boleh melihat/preview dokumen.
     * Sama dengan view — jika bisa lihat submission, bisa lihat dokumennya.
     */
    public function viewDocument(User $user, Submission $submission): bool
    {
        return $this->view($user, $submission);
    }

    /**
     * Apakah student pemilik boleh mengkonfirmasi data EC.
     */
    public function confirmEc(User $user, Submission $submission): bool
    {
        return $user->hasRole('student')
            && $submission->student_id === $user->id;
    }

    /**
     * Apakah ketua penandatangan boleh menandatangani sertifikat.
     */
    public function sign(User $user, Submission $submission): bool
    {
        return $user->hasRole('ketua')
            && $submission->signatory_id === $user->id;
    }

    /**
     * Apakah student pemilik boleh mengunduh sertifikat EC.
     */
    public function downloadEc(User $user, Submission $submission): bool
    {
        if ($user->hasRole('student')) {
            return $submission->student_id === $user->id;
        }
        // Non-student roles that can view all submissions
        return $user->hasAnyRole(['admin', 'sekretariat', 'ketua']);
    }

    /**
     * Apakah user boleh mengunduh sertifikat (multi-role).
     */
    public function downloadCertificate(User $user, Submission $submission): bool
    {
        if ($user->hasAnyRole(['admin'])) return true;
        if ($user->hasRole('sekretariat')) return $submission->secretary_id == $user->id;
        if ($user->hasRole('student') && $submission->student_id === $user->id) return true;
        if ($user->hasRole('ketua') && $submission->signatory_id === $user->id) return true;
        return false;
    }

    // ─── DocCheck Operations ─────────────────────────────────────

    /**
     * Apakah user boleh melihat daftar cek dokumen.
     */
    public function viewAnyDocCheck(User $user): bool
    {
        return $user->hasRole('sekretariat');
    }

    /**
     * Apakah user boleh melihat detail cek dokumen.
     */
    public function viewDocCheck(User $user, Submission $submission): bool
    {
        return $user->hasRole('sekretariat') && $submission->secretary_id == $user->id;
    }

    /**
     * Apakah user boleh menyetujui dokumen pengajuan.
     */
    public function approveDocCheck(User $user, Submission $submission): bool
    {
        return $user->hasRole('sekretariat')
            && $submission->secretary_id == $user->id
            && in_array($submission->status, [SubmissionStatus::PROCESS, SubmissionStatus::REVISED]);
    }

    /**
     * Apakah user boleh mengembalikan dokumen ke mahasiswa.
     */
    public function returnDocCheck(User $user, Submission $submission): bool
    {
        return $user->hasRole('sekretariat')
            && $submission->secretary_id == $user->id
            && in_array($submission->status, [SubmissionStatus::PROCESS, SubmissionStatus::REVISED]);
    }

    // ─── Decision Operations ─────────────────────────────────────

    /**
     * Apakah user boleh melihat daftar keputusan.
     */
    public function viewAnyDecision(User $user): bool
    {
        return $user->hasRole('sekretariat');
    }

    /**
     * Apakah user boleh melihat detail keputusan.
     */
    public function viewDecision(User $user, Submission $submission): bool
    {
        return $user->hasRole('sekretariat') && $submission->secretary_id == $user->id;
    }

    /**
     * Apakah user boleh membuat keputusan sidang.
     */
    public function createDecision(User $user, Submission $submission): bool
    {
        return $user->hasRole('sekretariat') && $submission->secretary_id == $user->id;
    }
}
