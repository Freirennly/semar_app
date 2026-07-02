<x-layouts.app :title="'Pemantauan & Riwayat KEP'">
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Pemantauan & Riwayat</h1>
        <p class="text-sm text-text-secondary mt-1.5">Audit menyeluruh riwayat keputusan etik pengajuan dan pemantauan performa penugasan reviewer.</p>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kiri: Tabel Riwayat Log Verifikasi & Penerbitan Komprehensif (70%) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h2 class="text-[20px] font-semibold text-text">Log Riwayat Sertifikasi Selesai</h2>
                    <span class="text-xs font-medium text-text-secondary bg-bg px-2.5 py-1 rounded-full border border-border">
                        Total Records: {{ $allVerifiedLogs->total() }}
                    </span>
                </div>

                @if($allVerifiedLogs->isEmpty())
                    <div class="text-center py-16 px-6">
                        <p class="text-sm text-text-muted italic">Belum ada rekaman riwayat data pengajuan sertifikat laik etik saat ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                                    <th class="px-6 py-4 font-semibold">Kode & Peneliti</th>
                                    <th class="px-6 py-4 font-semibold">Judul Usulan Penelitian</th>
                                    <th class="px-6 py-4 font-semibold">Aktivitas Sistem</th>
                                    <th class="px-6 py-4 font-semibold text-right">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($allVerifiedLogs as $log)
                                    <tr class="hover:bg-soft-surface/25 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-xs text-text-secondary block font-semibold">{{ $log->submission ? $log->submission->code : 'N/A' }}</span>
                                            <span class="text-xs text-text-muted mt-0.5 block">{{ $log->submission && $log->submission->student ? $log->submission->student->name : 'Unknown' }}</span>
                                        </td>
                                        <td class="px-6 py-4 max-w-[240px] truncate font-medium text-text" title="{{ $log->submission ? $log->submission->title : '' }}">
                                            {{ $log->submission ? \Illuminate\Support\Str::title($log->submission->title) : 'Unknown Submission' }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-text-secondary">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                                {{ $log->description }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right text-xs text-text-secondary whitespace-nowrap">
                                            {{ $log->created_at->format('d M Y, H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination Links --}}
                    <div class="px-6 py-4 border-t border-border bg-white">
                        {{ $allVerifiedLogs->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Kanan: Panel Monitoring Beban Kerja Reviewer Komite Etik (30%) --}}
        <div class="space-y-6">
            <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="text-[20px] font-semibold text-text">Beban Kerja Reviewer</h2>
                    <p class="text-xs text-text-secondary mt-1">Pantau distribusi penugasan berkas substansi aktif.</p>
                </div>

                <div class="divide-y divide-border overflow-y-auto max-h-[500px]">
                    @foreach($reviewersPerformance as $rev)
                        <div class="p-5 hover:bg-soft-surface/10 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-text">{{ $rev->name }}</span>
                                <span class="text-xs font-mono text-text-secondary bg-bg border border-border px-2 py-0.5 rounded">
                                    {{ $rev->total_tugas }} Berkas
                                </span>
                            </div>
                            
                            @php
                                $ratio = $rev->total_tugas > 0 ? ($rev->tugas_selesai / $rev->total_tugas) * 100 : 0;
                            @endphp
                            <div class="mt-2">
                                <div class="w-full bg-bg rounded-full h-1.5 border border-border overflow-hidden">
                                    <div class="bg-primary h-full rounded-full transition-all duration-500" style="width: {{ $ratio }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-text-muted mt-1.5">
                                    <span>Rasio Selesai: {{ round($ratio) }}%</span>
                                    <span class="font-medium text-text-secondary">{{ $rev->tugas_selesai }} Selesai / {{ $rev->total_tugas - $rev->tugas_selesai }} Pending</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>