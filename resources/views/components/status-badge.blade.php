@props(['status'])
@php
$s = is_string($status) ? $status : $status->value;
$map = [
    'DRAFT'            => ['Draft',               'bg-slate-100 text-slate-600 border border-slate-200'],
    'SUBMITTED'        => ['Diajukan',            'bg-[#E8F6FB] text-[#1A7FA0] border border-[#87CEEB]/30'],
    'DOC_CHECK'        => ['Cek Dokumen',         'bg-[#E8F6FB] text-[#1A7FA0] border border-[#87CEEB]/30'],
    'ASSIGNED'         => ['Reviewer Ditugaskan',  'bg-indigo-50 text-indigo-700 border border-indigo-200'],
    'UNDER_REVIEW'     => ['Sedang Direview',      'bg-violet-50 text-violet-700 border border-violet-200'],
    'PENDING_DECISION' => ['Menunggu Keputusan',   'bg-amber-50 text-amber-700 border border-amber-200'],
    'APPROVED'         => ['Disetujui',            'bg-emerald-50 text-emerald-700 border border-emerald-200'],
    'RESUBMISSION'     => ['Perlu Revisi',         'bg-orange-50 text-orange-700 border border-orange-200'],
    'DISAPPROVED'      => ['Ditolak',              'bg-red-50 text-red-700 border border-red-200'],
    'ARCHIVED'         => ['Diarsipkan',           'bg-slate-50 text-slate-500 border border-slate-200'],
];
$badge = $map[$s] ?? [$s, 'bg-slate-100 text-slate-500 border border-slate-200'];

// Dot color for active statuses
$dotMap = [
    'SUBMITTED'        => 'bg-[#87CEEB]',
    'DOC_CHECK'        => 'bg-[#87CEEB]',
    'ASSIGNED'         => 'bg-indigo-500',
    'UNDER_REVIEW'     => 'bg-violet-500',
    'PENDING_DECISION' => 'bg-amber-500',
    'APPROVED'         => 'bg-emerald-500',
    'RESUBMISSION'     => 'bg-orange-500',
    'DISAPPROVED'      => 'bg-red-500',
];
$dot = $dotMap[$s] ?? null;
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium {$badge[1]}"]) }}>
    @if($dot)<span class="w-1.5 h-1.5 rounded-full {{ $dot }}" aria-hidden="true"></span>@endif
    {{ $badge[0] }}
</span>
