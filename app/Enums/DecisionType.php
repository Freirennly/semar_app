<?php

namespace App\Enums;

enum DecisionType: string
{
    case APPROVED = 'APPROVED';
    case APPROVED_WITH_REVISION = 'APPROVED_WITH_REVISION';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Disetujui',
            self::APPROVED_WITH_REVISION => 'Disetujui dengan Revisi',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::APPROVED => 'bg-success-bg text-success border border-success/20',
            self::APPROVED_WITH_REVISION => 'bg-warning-bg text-warning border border-warning/20',
            self::REJECTED => 'bg-danger-bg text-danger border border-danger/20',
        };
    }
}
