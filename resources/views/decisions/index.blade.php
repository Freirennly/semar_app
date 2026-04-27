<x-layouts.app :title="'Keputusan'">
    <div class="mb-6"><h2 class="text-xl font-bold text-text">Keputusan</h2><p class="text-sm text-text-secondary mt-1">Pengajuan yang menunggu keputusan akhir setelah review selesai.</p></div>
    <div class="card">
        @if($submissions->isEmpty())
        <div class="text-center py-16"><div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-3"><svg class="w-7 h-7 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75"/></svg></div><p class="text-sm font-medium text-text">Tidak ada pengajuan yang menunggu keputusan</p></div>
        @else
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-left text-text-muted text-xs uppercase tracking-wider border-b border-border"><th class="px-6 py-3 font-medium">Kode</th><th class="px-6 py-3 font-medium">Judul</th><th class="px-6 py-3 font-medium hidden sm:table-cell">Pengaju</th><th class="px-6 py-3 font-medium">Review</th><th class="px-6 py-3 font-medium">Aksi</th></tr></thead>
        <tbody class="divide-y divide-border">@foreach($submissions as $sub)<tr class="hover:bg-soft-surface/30 transition-colors"><td class="px-6 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td><td class="px-6 py-3 font-medium text-text max-w-xs truncate">{{ $sub->title }}</td><td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $sub->student->name }}</td><td class="px-6 py-3 text-text-secondary">{{ $sub->reviews->count() }} review</td><td class="px-6 py-3"><a href="{{ route('decisions.show', $sub) }}" class="text-primary hover:text-primary-hover text-sm font-medium">Putuskan</a></td></tr>@endforeach</tbody></table></div>
        @endif
    </div>
</x-layouts.app>
