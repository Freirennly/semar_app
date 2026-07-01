<?php

namespace App\Enums;

enum Recommendation: string
{
    case APPROVE = 'APPROVE';
    case REVISION = 'REVISION';
    case REJECT = 'REJECT';
    case RECOMMEND_FULLBOARD = 'RECOMMEND_FULLBOARD';

    public function label(): string
    {
        return match ($this) {
            self::APPROVE => 'Recommend Approve',
            self::REVISION => 'Recommend Revision',
            self::REJECT => 'Recommend Reject',
            self::RECOMMEND_FULLBOARD => 'Recommend Fullboard',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::APPROVE => 'bg-success-bg text-success border border-success/20',
            self::REVISION => 'bg-warning-bg text-warning border border-warning/20',
            self::REJECT => 'bg-danger-bg text-danger border border-danger/20',
            self::RECOMMEND_FULLBOARD => 'bg-primary-bg text-primary border border-primary/20',
        };
    }
}
