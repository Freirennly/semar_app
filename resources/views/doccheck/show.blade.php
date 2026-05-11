<x-layouts.app :title="'Cek Dokumen: ' . $submission->title">
    <div class="mb-6">
        <nav class="text-sm text-text-muted mb-2" aria-label="Breadcrumb"><a href="{{ route('doccheck.index') }}" class="hover:text-primary">Cek Dokumen</a> <span class="mx-1">/</span> <span class="text-text">{{ $submission->code }}</span></nav>
        <div class="flex items-center gap-3"><h2 class="text-xl font-bold text-text">{{ $submission->title }}</h2><x-status-badge :status="$submission->status" /></div>
        <p class="text-sm text-text-secondary mt-1">{{ $submission->code }} · {{ $submission->student->name }}</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6">
            <h3 class="text-base font-semibold text-text mb-4">Dokumen yang Diupload</h3>
            <div class="space-y-3">
                @foreach(\App\Enums\DocType::cases() as $dtype)
                @php $doc = $submission->documents->firstWhere('doc_type', $dtype); @endphp
                <div class="flex items-center justify-between p-4 border border-border rounded-lg">
                    <div class="flex items-center gap-3">
                        @if($doc)<svg class="w-5 h-5 text-success shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @else<svg class="w-5 h-5 text-danger shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>@endif
                        <div><p class="text-sm font-medium text-text">{{ $dtype->label() }}</p>@if($doc)<p class="text-xs text-text-muted">{{ $doc->original_name }}</p>@else<p class="text-xs text-danger">Tidak ditemukan</p>@endif</div>
                    </div>
                    @if($doc)<a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn-outline text-xs px-3 py-1.5">Lihat</a>@endif
                </div>
                @endforeach
            </div>
        </div>
        <div class="card p-6">
            <h3 class="text-base font-semibold text-text mb-4">Aksi</h3>
            @if($submission->hasAllDocuments())
            <p class="text-sm text-success bg-success-bg border border-success/20 rounded-lg px-4 py-3 mb-4">Semua dokumen lengkap.</p>
            @else
            <p class="text-sm text-warning bg-warning-bg border border-warning/20 rounded-lg px-4 py-3 mb-4">Dokumen belum lengkap ({{ $submission->getDocumentCount() }} / {{ $submission->getRequiredDocumentCount() }}).</p>
            @endif
            <form method="POST" action="{{ route('doccheck.approve', $submission) }}" class="mb-3">@csrf
                <button type="submit" class="w-full btn-primary py-2.5 {{ !$submission->hasAllDocuments() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$submission->hasAllDocuments() ? 'disabled' : '' }} aria-label="Terima dokumen">Terima Dokumen</button>
            </form>
            <form method="POST" action="{{ route('doccheck.return', $submission) }}">@csrf
                <div class="mb-3"><label for="note" class="block text-xs font-medium text-text-secondary mb-1">Catatan (wajib saat mengembalikan)</label><textarea name="note" id="note" rows="3" class="input-field" placeholder="Jelaskan apa yang perlu diperbaiki..."></textarea></div>
                <button type="submit" class="w-full btn-outline py-2.5 text-warning border-warning/30 hover:bg-warning-bg" aria-label="Kembalikan ke student">Kembalikan ke Student</button>
            </form>
        </div>
    </div>
</x-layouts.app>
