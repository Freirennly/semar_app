<x-layouts.app :title="'Keputusan: ' . $submission->title">
    <div class="mb-6">
        <nav class="text-sm text-text-muted mb-2" aria-label="Breadcrumb"><a href="{{ route('decisions.index') }}" class="hover:text-primary">Keputusan</a> <span class="mx-1">/</span> <span class="text-text">{{ $submission->code }}</span></nav>
        <div class="flex items-center gap-3"><h2 class="text-xl font-bold text-text">{{ $submission->title }}</h2><x-status-badge :status="$submission->status" /></div>
        <p class="text-sm text-text-secondary mt-1">{{ $submission->code }} · {{ $submission->student->name }}</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-base font-semibold text-text">Hasil Review</h3>
            @forelse($submission->reviews as $rev)
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <div><p class="text-sm font-semibold text-text">{{ $rev->reviewer->name }}</p><p class="text-xs text-text-muted">{{ $rev->submitted_at?->format('d M Y, H:i') ?? 'Belum submit' }}</p></div>
                    @if($rev->recommendation)<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-soft-surface text-primary border border-primary/20">{{ $rev->recommendation->label() }}</span>@endif
                </div>
                <p class="text-sm text-text-secondary">{{ $rev->notes ?? 'Belum ada catatan.' }}</p>
            </div>
            @empty
            <div class="card p-6 text-center"><p class="text-sm text-text-secondary">Belum ada review.</p></div>
            @endforelse
        </div>
        <div class="card p-6">
            <h3 class="text-base font-semibold text-text mb-4">Keputusan Akhir</h3>
            @if($submission->latestDecision)
            <div class="bg-soft-surface rounded-lg p-4">
                <p class="text-sm font-medium text-text">Keputusan sudah dibuat:</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium mt-2 bg-soft-surface text-primary border border-primary/20">{{ $submission->latestDecision->decision->label() }}</span>
                @if($submission->latestDecision->notes)<p class="text-sm text-text-secondary mt-2">{{ $submission->latestDecision->notes }}</p>@endif
            </div>
            @else
            <form method="POST" action="{{ route('decisions.store', $submission) }}">@csrf
                @if($errors->any())<div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <div class="mb-5">
                    <label class="block text-sm font-medium text-text mb-2">Keputusan <span class="text-danger">*</span></label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border border-border rounded-lg cursor-pointer hover:bg-soft-surface/50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary-light"><input type="radio" name="decision" value="APPROVED" required class="text-primary focus:ring-primary-ring" aria-label="Disetujui"><span class="text-sm font-medium text-text">Disetujui (Approved)</span></label>
                        <label class="flex items-center gap-3 p-3 border border-border rounded-lg cursor-pointer hover:bg-soft-surface/50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary-light"><input type="radio" name="decision" value="RESUBMISSION" class="text-primary focus:ring-primary-ring" aria-label="Perlu Revisi"><span class="text-sm font-medium text-text">Perlu Revisi (Resubmission)</span></label>
                        <label class="flex items-center gap-3 p-3 border border-border rounded-lg cursor-pointer hover:bg-soft-surface/50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary-light"><input type="radio" name="decision" value="DISAPPROVED" class="text-primary focus:ring-primary-ring" aria-label="Ditolak"><span class="text-sm font-medium text-text">Ditolak (Disapproved)</span></label>
                    </div>
                </div>
                <div class="mb-5"><label for="notes" class="block text-sm font-medium text-text mb-1.5">Catatan</label><textarea name="notes" id="notes" rows="4" class="input-field" placeholder="Alasan keputusan..."></textarea></div>
                <button type="submit" class="w-full btn-primary py-2.5" onclick="return confirm('Simpan keputusan akhir? Tindakan ini tidak dapat dibatalkan.')">Simpan Keputusan</button>
            </form>
            @endif
        </div>
    </div>
</x-layouts.app>
