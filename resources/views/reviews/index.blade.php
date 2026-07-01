<x-layouts.app :title="'Review Saya'">
    {{-- Header Section --}}
    <div class="mb-6 animate-fade-in">
        <h1 class="text-3xl md:text-[36px] font-bold text-text">Review Saya</h1>
        <p class="text-[14px] text-text-secondary mt-1">Daftar pengajuan yang ditugaskan untuk Anda review.</p>
    </div>

    {{-- Review List Card --}}
    <div class="card bg-white border border-border rounded-2xl overflow-hidden animate-fade-in" style="animation-delay: 100ms">
        @if($assignments->isEmpty())
        <div class="text-center py-16 px-6">
            <div class="w-16 h-16 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-text">Belum ada penugasan review</h3>
            <p class="text-[14px] text-text-secondary mt-1">Daftar penugasan new akan tampil di sini.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-[14px]">
                <thead>
                    <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                        <th class="px-6 py-4 font-semibold">Judul</th>
                        <th class="px-6 py-4 font-semibold hidden sm:table-cell">Pengaju</th>
                        <th class="px-6 py-4 font-semibold hidden sm:table-cell">Deadline</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @foreach($assignments as $a)
                    <tr class="hover:bg-soft-surface/25 transition-colors">
                        <td class="px-6 py-4 font-semibold text-text max-w-xs truncate">{{ $a->submission->title }}</td>
                        <td class="px-6 py-4 text-text-secondary hidden sm:table-cell">{{ $a->submission->student->name }}</td>
                        <td class="px-6 py-4 text-text-secondary hidden sm:table-cell">{{ $a->due_at ? $a->due_at->timezone('Asia/Jakarta')->format('d M Y') : '—' }}</td>
                        <td class="px-6 py-4">
                            @if($a->status === 'COMPLETED')
                                <span class="text-[12px] font-semibold text-green-600">Selesai</span>
                            @else
                                <span class="text-[12px] font-semibold text-orange-600">Belum</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('reviews.show', $a->submission) }}" class="text-primary hover:text-primary-hover font-bold transition-colors">
                                {{ $a->status === 'COMPLETED' ? 'Lihat' : 'Isi Review' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>
