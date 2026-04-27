<x-layouts.app :title="'Dashboard'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Selamat datang, {{ auth()->user()->name }}</h2>
        <p class="text-sm text-text-secondary mt-1">Berikut ringkasan pengajuan Anda.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @foreach($metrics as $m)
            <x-metric-card :label="$m['label']" :value="$m['value']" :color="$m['color']" />
        @endforeach
    </div>

    <div class="card">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
            <h3 class="text-base font-semibold text-text">Pengajuan Saya</h3>
            <a href="{{ route('submissions.create') }}" class="btn-primary" aria-label="Buat pengajuan baru">+ Buat Pengajuan</a>
        </div>
        @if($submissions->isEmpty())
        <div class="text-center py-16 px-6">
            <div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
            <p class="text-sm font-medium text-text">Belum ada pengajuan</p>
            <p class="text-sm text-text-secondary mt-1">Buat pengajuan pertama Anda untuk memulai.</p>
            <a href="{{ route('submissions.create') }}" class="inline-block mt-4 btn-primary">+ Buat Pengajuan Pertama</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted text-xs uppercase tracking-wider">
                        <th class="px-6 py-3 font-medium">Kode</th>
                        <th class="px-6 py-3 font-medium">Judul</th>
                        <th class="px-6 py-3 font-medium hidden sm:table-cell">Jenis</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium hidden sm:table-cell">Tanggal</th>
                        <th class="px-6 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($submissions as $sub)
                    <tr class="hover:bg-soft-surface/30 transition-colors">
                        <td class="px-6 py-3 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                        <td class="px-6 py-3 font-medium text-text max-w-xs truncate">{{ $sub->title }}</td>
                        <td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $sub->type }}</td>
                        <td class="px-6 py-3"><x-status-badge :status="$sub->status" /></td>
                        <td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $sub->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('submissions.show', $sub) }}" class="text-primary hover:text-primary-hover text-sm font-medium">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>
