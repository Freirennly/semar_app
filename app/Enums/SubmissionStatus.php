<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case DOC_CHECK = 'DOC_CHECK';
    case ASSIGNED = 'ASSIGNED';
    case UNDER_REVIEW = 'UNDER_REVIEW';
    case PENDING_DECISION = 'PENDING_DECISION';
    case APPROVED = 'APPROVED';
    case RESUBMISSION = 'RESUBMISSION';
    case DISAPPROVED = 'DISAPPROVED';
    case ARCHIVED = 'ARCHIVED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan',
            self::DOC_CHECK => 'Pemeriksaan Dokumen',
            self::ASSIGNED => 'Reviewer Ditugaskan',
            self::UNDER_REVIEW => 'Sedang Direview',
            self::PENDING_DECISION => 'Menunggu Keputusan',
            self::APPROVED => 'Disetujui',
            self::RESUBMISSION => 'Perlu Revisi',
            self::DISAPPROVED => 'Ditolak',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-slate-600',
            self::SUBMITTED => 'bg-blue-50 text-blue-700',
            self::DOC_CHECK => 'bg-sky-50 text-sky-700',
            self::ASSIGNED => 'bg-indigo-50 text-indigo-700',
            self::UNDER_REVIEW => 'bg-violet-50 text-violet-700',
            self::PENDING_DECISION => 'bg-amber-50 text-amber-700',
            self::APPROVED => 'bg-emerald-50 text-emerald-700',
            self::RESUBMISSION => 'bg-orange-50 text-orange-700',
            self::DISAPPROVED => 'bg-red-50 text-red-700',
            self::ARCHIVED => 'bg-slate-100 text-slate-500',
        };
    }

    public function borderColor(): string
    {
        return match ($this) {
            self::DRAFT => 'border-l-slate-400',
            self::SUBMITTED => 'border-l-blue-500',
            self::DOC_CHECK => 'border-l-sky-500',
            self::ASSIGNED => 'border-l-indigo-500',
            self::UNDER_REVIEW => 'border-l-violet-500',
            self::PENDING_DECISION => 'border-l-amber-500',
            self::APPROVED => 'border-l-emerald-500',
            self::RESUBMISSION => 'border-l-orange-500',
            self::DISAPPROVED => 'border-l-red-500',
            self::ARCHIVED => 'border-l-slate-400',
        };
    }
}
