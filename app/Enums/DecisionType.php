<?php

namespace App\Enums;

enum DecisionType: string
{
    case APPROVED = 'APPROVED';
    case RESUBMISSION = 'RESUBMISSION';
    case DISAPPROVED = 'DISAPPROVED';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Disetujui',
            self::RESUBMISSION => 'Perlu Revisi (Resubmisi)',
            self::DISAPPROVED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::APPROVED => 'bg-success-bg text-success border border-success/20',
            self::RESUBMISSION => 'bg-warning-bg text-warning border border-warning/20',
            self::DISAPPROVED => 'bg-danger-bg text-danger border border-danger/20',
        };
    }
}
