<x-layouts.app :title="'Dashboard Sekretariat'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Dashboard Sekretariat</h2>
        <p class="text-sm text-text-secondary mt-1">Kelola validasi dokumen dan keputusan akhir pengajuan.</p>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($metrics as $m)
            <x-metric-card :label="$m['label']" :value="$m['value']" :color="$m['color']" />
        @endforeach
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="text-base font-semibold text-text">Perlu Cek Dokumen</h3>
                <a href="{{ route('doccheck.index') }}" class="text-sm text-primary hover:text-primary-hover font-medium">Lihat semua →</a>
            </div>
            @forelse($submitted as $sub)
            <div class="px-6 py-3 border-b border-border last:border-b-0 flex items-center justify-between hover:bg-soft-surface/30 transition-colors">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-text truncate">{{ $sub->title }}</p>
                    <p class="text-xs text-text-muted">{{ $sub->student->name }} · {{ $sub->created_at->format('d M Y') }}</p>
                </div>
                <a href="{{ route('doccheck.show', $sub) }}" class="text-primary hover:text-primary-hover text-sm font-medium shrink-0 ml-4">Cek</a>
            </div>
            @empty
            <div class="text-center py-10"><p class="text-sm text-text-secondary">Tidak ada pengajuan baru.</p></div>
            @endforelse
        </div>
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="text-base font-semibold text-text">Menunggu Keputusan</h3>
                <a href="{{ route('decisions.index') }}" class="text-sm text-primary hover:text-primary-hover font-medium">Lihat semua →</a>
            </div>
            @forelse($pendingDecision as $sub)
            <div class="px-6 py-3 border-b border-border last:border-b-0 flex items-center justify-between hover:bg-soft-surface/30 transition-colors">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-text truncate">{{ $sub->title }}</p>
                    <p class="text-xs text-text-muted">{{ $sub->reviews->count() }} review masuk</p>
                </div>
                <a href="{{ route('decisions.show', $sub) }}" class="text-primary hover:text-primary-hover text-sm font-medium shrink-0 ml-4">Putuskan</a>
            </div>
            @empty
            <div class="text-center py-10"><p class="text-sm text-text-secondary">Tidak ada pengajuan menunggu keputusan.</p></div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
