<?php

namespace App\Enums;

enum DocType: string
{
    case PROPOSAL = 'PROPOSAL';
    case ICF = 'ICF';
    case SURAT_PENGANTAR = 'SURAT_PENGANTAR';

    public function label(): string
    {
        return match ($this) {
            self::PROPOSAL => 'Proposal Penelitian',
            self::ICF => 'Informed Consent (ICF)',
            self::SURAT_PENGANTAR => 'Surat Pengantar / Dokumen Sponsor',
        };
    }
}
