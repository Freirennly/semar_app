@props(['status'])
@php
$s = is_string($status) ? $status : $status->value;
$map = [
    'NEW_PROPOSAL'      => ['Proposal Baru',            'text-blue-600'],
    'PROCESS'           => ['Diproses Sekretariat',      'text-blue-600'],
    'ON_REVIEW'         => ['Sedang Direview',           'text-purple-600'],
    'APPROVED'          => ['Disetujui',                 'text-green-600'],
    'REVISION_REQUIRED' => ['Perlu Revisi',              'text-orange-600'],
    'REVISED'           => ['Revisi Dikirim',            'text-blue-600'],
    'REJECTED'          => ['Ditolak',                   'text-red-600'],
    'WAITING_SIGNATURE' => ['Menunggu Tanda Tangan',     'text-purple-600'],
    'DONE'              => ['Selesai',                   'text-green-600'],
];
$badge = $map[$s] ?? [$s, 'text-slate-600'];
@endphp
<span {{ $attributes->merge(['class' => "text-[12px] font-semibold {$badge[1]} whitespace-nowrap"]) }}>
    {{ $badge[0] }}
</span>
