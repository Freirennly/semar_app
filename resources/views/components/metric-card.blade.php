@props(['label', 'value', 'color' => 'primary', 'icon' => null])
@php
$styles = [
    'primary' => ['border-l-primary', 'bg-soft-surface text-primary'],
    'info'    => ['border-l-info', 'bg-[#E8F6FB] text-[#1A7FA0]'],
    'warning' => ['border-l-warning', 'bg-warning-bg text-warning'],
    'danger'  => ['border-l-danger', 'bg-danger-bg text-danger'],
    'success' => ['border-l-success', 'bg-success-bg text-success'],
    'muted'   => ['border-l-text-muted', 'bg-slate-100 text-slate-500'],
    // Backward compatibility
    'blue'    => ['border-l-primary', 'bg-soft-surface text-primary'],
    'amber'   => ['border-l-warning', 'bg-warning-bg text-warning'],
    'violet'  => ['border-l-primary', 'bg-soft-surface text-primary'],
    'red'     => ['border-l-danger', 'bg-danger-bg text-danger'],
    'emerald' => ['border-l-success', 'bg-success-bg text-success'],
    'slate'   => ['border-l-text-muted', 'bg-slate-100 text-slate-500'],
];
$s = $styles[$color] ?? ['border-l-primary', 'bg-soft-surface text-primary'];
@endphp
<div class="card border-l-4 {{ $s[0] }} p-5 hover:shadow-md transition-all duration-200 group">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-text-secondary">{{ $label }}</p>
            <p class="text-2xl font-bold text-text mt-1 group-hover:text-primary transition-colors duration-200">{{ $value }}</p>
        </div>
        @if($icon)
        <div class="w-10 h-10 rounded-lg {{ $s[1] }} flex items-center justify-center shrink-0">
            {!! $icon !!}
        </div>
        @endif
    </div>
</div>
