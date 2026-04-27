@props(['label', 'value', 'color' => 'primary'])
@php
$styles = [
    'primary' => 'border-l-primary',
    'info'    => 'border-l-info',
    'warning' => 'border-l-warning',
    'danger'  => 'border-l-danger',
    'success' => 'border-l-success',
    'muted'   => 'border-l-text-muted',
    // Backward compatibility
    'blue'    => 'border-l-primary',
    'amber'   => 'border-l-warning',
    'violet'  => 'border-l-primary',
    'red'     => 'border-l-danger',
    'emerald' => 'border-l-success',
    'slate'   => 'border-l-text-muted',
];
$b = $styles[$color] ?? 'border-l-primary';
@endphp
<div class="card border-l-4 {{ $b }} p-5">
    <p class="text-sm text-text-secondary">{{ $label }}</p>
    <p class="text-2xl font-bold text-text mt-1">{{ $value }}</p>
</div>
