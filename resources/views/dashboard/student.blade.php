<x-layouts.app :title="'Dashboard'">
    {{-- Welcome Section --}}
    <div class="mb-6 animate-fade-in">
        <h2 class="text-xl font-bold text-text">Selamat datang, {{ auth()->user()->name }}</h2>
        <p class="text-sm text-text-secondary mt-1">Berikut ringkasan pengajuan Anda.</p>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @php
        $icons = [
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>',
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>',
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.182-3.182"/></svg>',
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        ];
        @endphp
        @foreach($metrics as $i => $m)
            <div class="animate-slide-up" style="animation-delay: {{ $i * 80 }}ms">
                <x-metric-card :label="$m['label']" :value="$m['value']" :color="$m['color']" :icon="$icons[$i] ?? null" />
            </div>
        @endforeach
    </div>

    {{-- Submissions Table --}}
    <div class="card animate-fade-in" style="animation-delay: 200ms">
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
                        <th class="px-6 py-3 font-medium hidden sm:table-cell">Tanggal Pengajuan</th>
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
                        <td class="px-6 py-3 flex items-center gap-4">
                            <a href="{{ route('submissions.show', $sub) }}" class="text-primary hover:text-primary-hover text-sm font-medium transition-colors">Lihat</a>
                            
                            {{-- Logika Dinamis Ethical Clearance --}}
                            @if($sub->status === \App\Enums\SubmissionStatus::KIRIM_USER)
                                <form action="{{ route('submissions.confirm', $sub) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:text-amber-800 text-sm font-medium transition-colors" onclick="return confirm('Pastikan draf sertifikat sudah sesuai. Lanjutkan konfirmasi?')">
                                        Konfirmasi Data
                                    </button>
                                </form>
                            @elseif($sub->status === \App\Enums\SubmissionStatus::PUBLISHED)
                                <a href="{{ route('submissions.download-ec', $sub) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium transition-colors flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh Sertifikat
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>