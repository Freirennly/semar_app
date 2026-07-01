<x-layouts.app :title="'Penugasan Reviewer'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Penugasan Reviewer</h2>
        <p class="text-sm text-text-secondary mt-1">Assign reviewer ke pengajuan yang sudah lolos validasi dokumen.</p>
    </div>
    @foreach($submissions as $sub)
    <div class="card mb-4 p-6">
        @php
            $totalAssignments = $sub->assignments->count();
            $completedAssignments = $sub->assignments->where('status', 'COMPLETED')->count();
            
            $statusText = 'ASSIGNED';
            $statusClass = 'text-orange-600 bg-orange-50 border border-orange-200';
            
            if ($totalAssignments > 0 && $completedAssignments === $totalAssignments) {
                $statusText = 'COMPLETED';
                $statusClass = 'text-green-600 bg-green-50 border border-green-200';
            }
        @endphp
        <div class="flex items-center justify-between mb-3">
            <div class="min-w-0">
                <h3 class="text-sm font-semibold text-text truncate">{{ $sub->title }}</h3>
                <p class="text-xs text-text-muted mt-1 flex flex-wrap items-center gap-2">
                    <span>{{ $sub->code }}</span>
                    <span>·</span>
                    <span>{{ $sub->student->name }}</span>
                    <span>·</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                    <span>·</span>
                    <span class="font-medium text-text-secondary bg-slate-100 px-2 py-0.5 rounded text-[10px]">Completed Reviews: {{ $completedAssignments }}/2</span>
                </p>
            </div>
            <a href="{{ route('submissions.show', $sub) }}" class="text-xs text-primary hover:text-primary-hover font-medium shrink-0 ml-3">Lihat detail →</a>
        </div>
        @if($sub->assignments->isNotEmpty())
        <div class="mb-3">
            <p class="text-xs text-text-muted font-medium uppercase tracking-wider mb-2">Reviewer Ditugaskan</p>
            <div class="flex flex-wrap gap-2">
                @foreach($sub->assignments as $a)
                <div class="flex items-center gap-2 bg-soft-surface rounded-lg px-3 py-1.5 text-sm border border-primary/10">
                    <span class="text-text">{{ $a->reviewer->name }}</span>
                    @if($a->due_at)<span class="text-xs text-text-muted">· due {{ $a->due_at->timezone('Asia/Jakarta')->format('d M') }}</span>@endif
                    <form method="POST" action="{{ route('assignments.destroy', $a) }}" class="inline">@csrf @method('DELETE')
                        <button type="submit" class="text-danger hover:text-danger/80 text-xs" onclick="return confirm('Hapus penugasan?')" aria-label="Hapus penugasan {{ $a->reviewer->name }}">×</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route('assignments.store') }}" class="flex flex-col sm:flex-row items-end gap-3 pt-3 border-t border-border">
            @csrf
            <input type="hidden" name="submission_id" value="{{ $sub->id }}">
            <div class="flex-1 w-full sm:w-auto">
                <label class="block text-xs font-medium text-text-secondary mb-1">Reviewer</label>
                <select name="reviewer_id" required class="input-field" aria-label="Pilih reviewer">
                    <option value="">Pilih reviewer...</option>
                    @foreach($reviewers as $rev)<option value="{{ $rev->id }}">{{ $rev->name }} ({{ $rev->email }})</option>@endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-medium text-text-secondary mb-1">Deadline</label>
                <input type="date" name="due_at" min="{{ date('Y-m-d') }}" class="input-field" aria-label="Deadline review">
            </div>
            @php
                $bothCompleted = ($totalAssignments === 2 && $completedAssignments === 2);
            @endphp
            <button type="submit" @if($bothCompleted) disabled class="btn-primary shrink-0 w-full sm:w-auto opacity-50 cursor-not-allowed" @else class="btn-primary shrink-0 w-full sm:w-auto" @endif>Assign</button>
        </form>
    </div>
    @endforeach
    @if($submissions->isEmpty())
    <div class="card text-center py-16">
        <div class="w-14 h-14 rounded-full bg-soft-surface mx-auto flex items-center justify-center mb-3"><svg class="w-7 h-7 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <p class="text-sm font-medium text-text">Tidak ada pengajuan yang perlu assign reviewer</p>
    </div>
    @endif
</x-layouts.app>
