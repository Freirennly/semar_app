<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case NEW_PROPOSAL = 'NEW_PROPOSAL';
    case PROCESS = 'PROCESS';
    case ON_REVIEW = 'ON_REVIEW';
    case APPROVED = 'APPROVED';
    case REVISION_REQUIRED = 'REVISION_REQUIRED';
    case REVISED = 'REVISED';
    case REJECTED = 'REJECTED';
    case WAITING_STUDENT_CONFIRMATION = 'WAITING_STUDENT_CONFIRMATION';
    case WAITING_SIGNATURE = 'WAITING_SIGNATURE';
    case DONE = 'DONE';
    case DRAFT_OLD = 'DRAFT'; 
    case APPROVED_WITH_REVISION = 'APPROVED_WITH_REVISION';

    public function label(): string
    {
        return match ($this) {
            self::NEW_PROPOSAL => 'Proposal Baru',
            self::PROCESS => 'Diproses Sekretariat',
            self::ON_REVIEW => 'Sedang Direview',
            self::APPROVED => 'Disetujui',
            self::REVISION_REQUIRED => 'Perlu Revisi',
            self::REVISED => 'Revisi Dikirim',
            self::REJECTED => 'Ditolak',
            self::WAITING_STUDENT_CONFIRMATION => 'Menunggu Konfirmasi Mahasiswa',
            self::WAITING_SIGNATURE => 'Menunggu Tanda Tangan',
            self::DONE => 'Selesai',
            
            // Pemetaan label untuk status variasi alternatif
            self::DRAFT_OLD => 'Draft',
            self::APPROVED_WITH_REVISION => 'Disetujui dengan Revisi',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NEW_PROPOSAL => 'bg-blue-50 text-blue-700',
            self::PROCESS => 'bg-cyan-50 text-cyan-700',
            self::ON_REVIEW => 'bg-violet-50 text-violet-700',
            self::APPROVED => 'bg-emerald-50 text-emerald-700',
            self::REVISION_REQUIRED => 'bg-orange-50 text-orange-700',
            self::REVISED => 'bg-teal-50 text-teal-700',
            self::REJECTED => 'bg-red-50 text-red-700',
            self::WAITING_STUDENT_CONFIRMATION => 'bg-blue-50 text-blue-700',
            self::WAITING_SIGNATURE => 'bg-rose-50 text-rose-700',
            self::DONE => 'bg-emerald-100 text-emerald-800',
            
            // Pemetaan class style untuk status variasi alternatif
            self::DRAFT_OLD => 'bg-slate-100 text-slate-600',
            self::APPROVED_WITH_REVISION => 'bg-emerald-50 text-emerald-600 font-medium',
        };
    }

    public function borderColor(): string
    {
        return match ($this) {
            self::NEW_PROPOSAL => 'border-l-blue-500',
            self::PROCESS => 'border-l-cyan-500',
            self::ON_REVIEW => 'border-l-violet-500',
            self::APPROVED => 'border-l-emerald-500',
            self::REVISION_REQUIRED => 'border-l-orange-500',
            self::REVISED => 'border-l-teal-500',
            self::REJECTED => 'border-l-red-500',
            self::WAITING_STUDENT_CONFIRMATION => 'border-l-blue-500',
            self::WAITING_SIGNATURE => 'border-l-rose-500',
            self::DONE => 'border-l-emerald-600',
            
            // Pemetaan border color untuk status variasi alternatif
            self::DRAFT_OLD => 'border-l-slate-400',
            self::APPROVED_WITH_REVISION => 'border-l-emerald-400',
        };
    }
}