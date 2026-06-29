<x-layouts.app :title="'Review: ' . $submission->title">
    {{-- Header & Breadcrumb --}}
    <div class="mb-6 animate-fade-in">
        <nav class="text-[12px] text-text-secondary mb-2" aria-label="Breadcrumb">
            <a href="{{ route('reviews.index') }}" class="hover:text-primary transition-colors">Review Saya</a> 
            <span class="mx-1">/</span> 
            <span class="text-text">{{ $submission->code }}</span>
        </nav>
        <div class="flex items-start md:items-center justify-between gap-4 flex-wrap">
            <div class="space-y-2">
                <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight">{{ $submission->title }}</h1>
                <p class="text-[14px] text-text-secondary">
                    <span class="font-semibold text-text">{{ $submission->code }}</span> · {{ $submission->student->name }}
                </p>
            </div>
            <x-status-badge :status="$submission->status" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Submission Info & Docs --}}
        <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6 h-fit">
            <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Dokumen</h2>
            
            <div class="space-y-4">
                @foreach($submission->documents as $doc)
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-success shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <div class="min-w-0">
                        @if($doc->type === 'file')
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-[14px] text-primary hover:text-primary-hover font-semibold transition-colors">{{ $doc->documentTemplate->name ?? 'Dokumen' }}</a>
                            <p class="text-[12px] text-text-secondary truncate mt-0.5">{{ $doc->original_name }}</p>
                        @else
                            <a href="{{ $doc->file_path }}" target="_blank" class="text-[14px] text-primary hover:text-primary-hover font-semibold transition-colors">{{ $doc->documentTemplate->name ?? 'Dokumen' }}</a>
                            <p class="text-[12px] text-text-secondary truncate mt-0.5">Google Drive Link</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($submission->abstract)
            <div class="pt-4 border-t border-border space-y-2">
                <span class="text-text-secondary text-[12px] font-semibold block uppercase tracking-wide">Abstrak</span>
                <div class="font-academic text-text p-4 bg-slate-50 border border-border rounded-xl whitespace-pre-line">{{ $submission->abstract }}</div>
            </div>
            @endif
        </div>

        {{-- Right: Review Form --}}
        <div class="lg:col-span-2 card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
            <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Form Review</h2>
            
            @if($review && $review->submitted_at)
            <div class="bg-soft-surface rounded-xl p-5 space-y-4 border border-primary/10">
                <p class="text-[14px] font-semibold text-text">Review telah dikirim pada {{ $review->submitted_at->format('d M Y, H:i') }}</p>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-md text-[12px] font-semibold bg-primary/10 text-primary border border-primary/20">
                        Rekomendasi: {{ $review->recommendation->label() }}
                    </span>
                </div>
                <div class="text-[14px] text-text-secondary bg-white p-4 border border-border rounded-xl whitespace-pre-line leading-relaxed">
                    {{ $review->notes }}
                </div>
            </div>
            @else
            <form method="POST" action="{{ route('reviews.store', $submission) }}" class="space-y-6">
                @csrf
                @if($errors->any())
                    <div class="mb-6 bg-danger-bg border border-danger/20 text-danger rounded-xl px-5 py-3 text-[14px] shadow-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-2">
                    <label for="notes" class="text-[14px] font-semibold text-text-secondary block">Catatan Review <span class="text-danger">*</span></label>
                    <textarea name="notes" id="notes" rows="6" required class="input-field" placeholder="Tulis catatan review Anda: kekuatan, kelemahan, saran perbaikan...">{{ old('notes', $review?->notes) }}</textarea>
                </div>

                <div class="space-y-3">
                    <label class="text-[14px] font-semibold text-text-secondary block">Rekomendasi <span class="text-danger">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach(\App\Enums\Recommendation::cases() as $rec)
                        <label class="flex items-center gap-3 p-4 border border-border rounded-xl cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-primary has-[:checked]:bg-primary-light">
                            <input type="radio" name="recommendation" value="{{ $rec->value }}" required {{ old('recommendation', $review?->recommendation?->value) === $rec->value ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-primary-ring" aria-label="{{ $rec->label() }}">
                            <span class="text-[14px] font-semibold text-text">{{ $rec->label() }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-border">
                    <button type="submit" class="btn-primary" onclick="return confirm('Submit review ini? Anda tidak dapat mengubahnya setelah submit.')">
                        Submit Review
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-layouts.app>
