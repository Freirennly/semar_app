<x-layouts.app :title="'Edit Pengajuan'">
    <div class="mb-12 flex items-center gap-3 animate-fade-in">
        <a href="{{ route('admin.proposals.index') }}" class="p-2 rounded-lg bg-white border border-border text-text-secondary hover:text-primary hover:border-primary/30 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Edit Pengajuan</h1>
            <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Perbarui status atau judul pengajuan.</p>
        </div>
    </div>

    <div class="card p-6 border-primary/10 shadow-sm max-w-3xl mb-12">
        <form method="POST" action="{{ route('admin.proposals.update', $proposal) }}" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Kode Pengajuan</label>
                    <input type="text" value="{{ $proposal->code }}" class="input-field bg-bg cursor-not-allowed" disabled>
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Pengusul (Mahasiswa)</label>
                    <input type="text" value="{{ optional($proposal->student)->name ?? 'Unknown' }}" class="input-field bg-bg cursor-not-allowed" disabled>
                </div>
            </div>

            <div>
                <label for="title" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Judul Pengajuan</label>
                <textarea name="title" id="title" rows="2" required class="input-field">{{ old('title', $proposal->title) }}</textarea>
                @error('title')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Status Saat Ini</label>
                <select name="status" id="status" required class="input-field">
                    @php
                        $workflowService = app(\App\Services\WorkflowService::class);
                        $currentStatus = $proposal->status;
                        $allowed = $workflowService->getAllowedTransitions($currentStatus);
                        
                        $statusesToShow = [$currentStatus->value => $currentStatus->label()];
                        foreach ($allowed as $nextStatusVal) {
                            $enumCase = \App\Enums\SubmissionStatus::tryFrom($nextStatusVal);
                            if ($enumCase) {
                                $statusesToShow[$nextStatusVal] = $enumCase->label();
                            }
                        }
                    @endphp
                    @foreach($statusesToShow as $val => $label)
                        <option value="{{ $val }}" {{ (old('status', $proposal->status->value ?? $proposal->status) == $val) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border mt-6">
                <a href="{{ route('admin.proposals.index') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary px-8">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-layouts.app>
