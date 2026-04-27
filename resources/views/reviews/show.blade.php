<x-layouts.app :title="'Review: ' . $submission->title">
    <div class="mb-6">
        <nav class="text-sm text-text-muted mb-2" aria-label="Breadcrumb"><a href="{{ route('reviews.index') }}" class="hover:text-primary">Review Saya</a> <span class="mx-1">/</span> <span class="text-text">{{ $submission->code }}</span></nav>
        <div class="flex items-center gap-3"><h2 class="text-xl font-bold text-text">{{ $submission->title }}</h2><x-status-badge :status="$submission->status" /></div>
        <p class="text-sm text-text-secondary mt-1">{{ $submission->code }} · {{ $submission->student->name }}</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card p-6">
            <h3 class="text-base font-semibold text-text mb-4">Dokumen</h3>
            <div class="space-y-3">
                @foreach($submission->documents as $doc)
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-success shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <div class="min-w-0">
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-sm text-primary hover:text-primary-hover font-medium">{{ $doc->doc_type->label() }}</a>
                        <p class="text-xs text-text-muted">{{ $doc->original_name }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @if($submission->abstract)
            <div class="mt-4 pt-4 border-t border-border">
                <p class="text-xs font-medium text-text-muted uppercase tracking-wider mb-1">Abstrak</p>
                <p class="text-sm text-text-secondary">{{ $submission->abstract }}</p>
            </div>
            @endif
        </div>
        <div class="lg:col-span-2 card p-6">
            <h3 class="text-base font-semibold text-text mb-4">Form Review</h3>
            @if($review && $review->submitted_at)
            <div class="bg-soft-surface rounded-lg p-4">
                <p class="text-sm font-medium text-text">Review sudah disubmit pada {{ $review->submitted_at->format('d M Y, H:i') }}</p>
                <div class="mt-2"><span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-soft-surface text-primary border border-primary/20">{{ $review->recommendation->label() }}</span></div>
                <p class="text-sm text-text-secondary mt-2">{{ $review->notes }}</p>
            </div>
            @else
            <form method="POST" action="{{ route('reviews.store', $submission) }}">@csrf
                @if($errors->any())<div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <div class="mb-5">
                    <label for="notes" class="block text-sm font-medium text-text mb-1.5">Catatan Review <span class="text-danger">*</span></label>
                    <textarea name="notes" id="notes" rows="6" required class="input-field" placeholder="Tulis catatan review Anda: kekuatan, kelemahan, saran perbaikan...">{{ old('notes', $review?->notes) }}</textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-text mb-2">Rekomendasi <span class="text-danger">*</span></label>
                    <div class="space-y-2">
                        @foreach(\App\Enums\Recommendation::cases() as $rec)
                        <label class="flex items-center gap-3 p-3 border border-border rounded-lg cursor-pointer hover:bg-soft-surface/50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary-light">
                            <input type="radio" name="recommendation" value="{{ $rec->value }}" required {{ old('recommendation', $review?->recommendation?->value) === $rec->value ? 'checked' : '' }} class="text-primary focus:ring-primary-ring" aria-label="{{ $rec->label() }}">
                            <span class="text-sm font-medium text-text">{{ $rec->label() }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn-primary" onclick="return confirm('Submit review ini? Anda tidak dapat mengubahnya setelah submit.')">Submit Review</button>
            </form>
            @endif
        </div>
    </div>
</x-layouts.app>
