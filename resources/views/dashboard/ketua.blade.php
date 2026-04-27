<x-layouts.app :title="'Dashboard Ketua'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Dashboard Ketua</h2>
        <p class="text-sm text-text-secondary mt-1">Kelola penugasan reviewer untuk pengajuan masuk.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        @foreach($metrics as $m)
            <x-metric-card :label="$m['label']" :value="$m['value']" :color="$m['color']" />
        @endforeach
    </div>
    <div class="card">
        <div class="px-6 py-4 border-b border-border"><h3 class="text-base font-semibold text-text">Perlu Assign Reviewer</h3></div>
        @if($needAssign->isEmpty())
        <div class="text-center py-16">
            <div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-3"><svg class="w-7 h-7 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <p class="text-sm font-medium text-text">Tidak ada pengajuan yang perlu assign reviewer</p>
            <p class="text-sm text-text-secondary mt-1">Semua pengajuan sudah ditugaskan.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-text-muted text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 font-medium">Kode</th>
                    <th class="px-6 py-3 font-medium">Judul</th>
                    <th class="px-6 py-3 font-medium hidden sm:table-cell">Pengaju</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-border">
                    @foreach($needAssign as $sub)
                    <tr class="hover:bg-soft-surface/30 transition-colors">
                        <td class="px-6 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                        <td class="px-6 py-3 font-medium text-text max-w-xs truncate">{{ $sub->title }}</td>
                        <td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $sub->student->name }}</td>
                        <td class="px-6 py-3"><x-status-badge :status="$sub->status" /></td>
                        <td class="px-6 py-3"><a href="{{ route('assignments.index') }}?focus={{ $sub->id }}" class="text-primary hover:text-primary-hover text-sm font-medium">Assign</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>
