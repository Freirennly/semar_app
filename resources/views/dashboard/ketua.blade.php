<x-layouts.app :title="'Dashboard Ketua'">
    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-[32px] md:text-[36px] font-bold text-text tracking-tight">Dashboard Ketua</h1>
        <p class="text-sm text-text-secondary mt-1.5">Kelola penetapan penugasan reviewer dan pemantauan keputusan etik penelitian KEP.</p>
    </div>

    @php
        // Fetch decision stats and waiting decision list directly
        $totalApproved = \App\Models\Submission::whereIn('status', [
            \App\Enums\SubmissionStatus::APPROVED,
            \App\Enums\SubmissionStatus::APPROVED_WITH_REVISION,
            \App\Enums\SubmissionStatus::DONE
        ])->count();

        $totalRejected = \App\Models\Submission::where('status', \App\Enums\SubmissionStatus::REJECTED)->count();

        $waitingDecision = \App\Models\Submission::where('status', \App\Enums\SubmissionStatus::ON_REVIEW)
            ->has('reviews')
            ->with('student', 'reviews')
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    {{-- Ringkasan Statistik Keputusan & Tugas (Flat cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Perlu Assign Reviewer</p>
            <p class="text-[28px] font-bold text-warning mt-2 leading-none">{{ $needAssign->count() }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Sedang Direview</p>
            <p class="text-[28px] font-bold text-primary mt-2 leading-none">{{ $assigned->count() }}</p>
        </div>
        <div class="bg-white border border-border p-5 rounded-xl">
            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">Keputusan (Disetujui / Ditolak)</p>
            <p class="text-[28px] font-bold text-text mt-2 leading-none">
                <span class="text-success">{{ $totalApproved }}</span>
                <span class="text-text-muted mx-1">/</span>
                <span class="text-danger">{{ $totalRejected }}</span>
            </p>
        </div>
    </div>

    {{-- Main Grid (70/30 Split) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Tasks (70% or lg:col-span-2) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Perlu Assign Reviewer --}}
            <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-white">
                    <h2 class="text-[20px] font-semibold text-text">Perlu Assign Reviewer</h2>
                </div>
                @if($needAssign->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada pengajuan baru yang perlu di-assign reviewer.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                                    <th class="px-6 py-4 font-semibold">Kode</th>
                                    <th class="px-6 py-4 font-semibold">Judul Usulan</th>
                                    <th class="px-6 py-4 font-semibold font-medium">Pengaju</th>
                                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($needAssign as $sub)
                                    <tr class="hover:bg-soft-surface/25 transition-colors">
                                        <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-6 py-4 font-semibold text-text truncate max-w-[200px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-6 py-4 text-text-secondary">{{ optional($sub->student)->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('assignments.index') }}?focus={{ $sub->id }}" class="px-3.5 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors">
                                                Assign
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Proposal Dalam Review --}}
            <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-white">
                    <h2 class="text-[20px] font-semibold text-text">Proposal Dalam Review</h2>
                </div>
                @if($assigned->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada proposal yang sedang aktif direview.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-text-secondary text-[12px] uppercase tracking-wider border-b border-border bg-slate-50/70">
                                    <th class="px-6 py-4 font-semibold">Kode</th>
                                    <th class="px-6 py-4 font-semibold">Judul Usulan</th>
                                    <th class="px-6 py-4 font-semibold">Reviewer Ditugaskan</th>
                                    <th class="px-6 py-4 font-semibold text-right">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($assigned as $sub)
                                    <tr class="hover:bg-soft-surface/25 transition-colors">
                                        <td class="px-6 py-4 font-mono text-xs text-text-secondary">{{ $sub->code }}</td>
                                        <td class="px-6 py-4 font-semibold text-text truncate max-w-[180px]" title="{{ $sub->title }}">
                                            {{ \Illuminate\Support\Str::title($sub->title) }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-text-secondary">
                                            <div class="space-y-1">
                                                @foreach($sub->assignments as $asg)
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="truncate max-w-[110px]" title="{{ $asg->reviewer->name }}">{{ $asg->reviewer->name }}</span>
                                                        <span class="font-bold shrink-0 {{ $asg->status === 'COMPLETED' ? 'text-success' : 'text-warning' }}">
                                                            {{ $asg->status === 'COMPLETED' ? 'Selesai' : 'Belum' }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            @php
                                                $totalAsg = $sub->assignments->count();
                                                $doneAsg = $sub->assignments->where('status', 'COMPLETED')->count();
                                            @endphp
                                            <span class="text-xs font-bold text-text">{{ $doneAsg }} / {{ $totalAsg }} Selesai</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Waiting Final Decision (30% or lg:col-span-1) --}}
        <div class="space-y-6">
            <div class="bg-white border border-border rounded-xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="text-[20px] font-semibold text-text">Menunggu Keputusan</h2>
                </div>
                
                @if($waitingDecision->isEmpty())
                    <div class="text-center py-12 px-6">
                        <p class="text-xs text-text-muted italic">Tidak ada pengajuan menunggu keputusan komite.</p>
                    </div>
                @else
                    <div class="divide-y divide-border overflow-y-auto max-h-[350px]">
                        @foreach($waitingDecision as $sub)
                            <div class="p-4 hover:bg-soft-surface/20 transition-colors">
                                <p class="text-xs font-semibold text-text line-clamp-2">{{ \Illuminate\Support\Str::title($sub->title) }}</p>
                                <div class="flex items-center justify-between mt-3 text-[10px] text-text-secondary">
                                    <span class="font-mono">{{ $sub->code }}</span>
                                    <span class="font-bold text-primary">{{ $sub->reviews->count() }} Review Masuk</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Statistik Keputusan Visual --}}
            <div class="bg-white p-6 border border-border rounded-xl">
                <h2 class="text-[20px] font-semibold text-text mb-4">Statistik Keputusan</h2>
                <div class="space-y-4">
                    @php
                        $totalAll = max($totalApproved + $totalRejected, 1);
                        $approvedPercent = ($totalApproved / $totalAll) * 100;
                        $rejectedPercent = ($totalRejected / $totalAll) * 100;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-semibold text-text-secondary">Disetujui</span>
                            <span class="font-bold text-text">{{ $totalApproved }} ({{ round($approvedPercent) }}%)</span>
                        </div>
                        <div class="w-full bg-bg rounded-full h-2 overflow-hidden border border-border">
                            <div class="bg-success h-full rounded-full" style="width: {{ $approvedPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-semibold text-text-secondary">Ditolak</span>
                            <span class="font-bold text-text">{{ $totalRejected }} ({{ round($rejectedPercent) }}%)</span>
                        </div>
                        <div class="w-full bg-bg rounded-full h-2 overflow-hidden border border-border">
                            <div class="bg-danger h-full rounded-full" style="width: {{ $rejectedPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
