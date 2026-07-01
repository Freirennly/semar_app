<?php

namespace App\Enums;

enum DecisionType: string
{
    case APPROVED = 'APPROVED';
    case REVISION_REQUIRED = 'REVISION_REQUIRED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Disetujui',
            self::REVISION_REQUIRED => 'Perlu Revisi',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::APPROVED => 'bg-success-bg text-success border border-success/20',
            self::REVISION_REQUIRED => 'bg-warning-bg text-warning border border-warning/20',
            self::REJECTED => 'bg-danger-bg text-danger border border-danger/20',
        };
    }
}
