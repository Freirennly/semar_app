@props(['status'])
@php
$s = is_string($status) ? $status : $status->value;
$map = [
    'DRAFT'            => ['Draft',               'bg-soft-surface text-primary border border-primary/20'],
    'SUBMITTED'        => ['Diajukan',            'bg-info-soft text-text border border-info/30'],
    'DOC_CHECK'        => ['Cek Dokumen',         'bg-info-soft text-text border border-info/30'],
    'ASSIGNED'         => ['Reviewer Ditugaskan',  'bg-soft-surface text-primary border border-primary/20'],
    'UNDER_REVIEW'     => ['Sedang Direview',      'bg-soft-surface text-primary border border-primary/30'],
    'PENDING_DECISION' => ['Menunggu Keputusan',   'bg-warning-bg text-warning border border-warning/20'],
    'APPROVED'         => ['Disetujui',            'bg-success-bg text-success border border-success/20'],
    'RESUBMISSION'     => ['Perlu Revisi',         'bg-warning-bg text-warning border border-warning/20'],
    'DISAPPROVED'      => ['Ditolak',              'bg-danger-bg text-danger border border-danger/20'],
    'ARCHIVED'         => ['Diarsipkan',           'bg-bg text-text-muted border border-border'],
];
$badge = $map[$s] ?? [$s, 'bg-bg text-text-secondary border border-border'];
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium {$badge[1]}"]) }}>{{ $badge[0] }}</span>
