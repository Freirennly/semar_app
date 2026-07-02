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
                            <a href="{{ route('submissions.view-document', ['submission' => $submission->id, 'document' => $doc->id]) }}" target="_blank" class="text-[14px] text-primary hover:text-primary-hover font-semibold transition-colors">{{ $doc->documentTemplate->name ?? 'Dokumen' }}</a>
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

        {{-- Right: Review Form & History --}}
        <div class="lg:col-span-2 space-y-6">
            @php
                $currentRound = $submission->decisions()->where('decision', \App\Enums\DecisionType::REVISION_REQUIRED->value)->count() + 1;
                $previousReviews = $submission->reviews->whereNotNull('submitted_at')
                    ->where('revision_round', '<', $currentRound)
                    ->where('reviewer_id', auth()->id())
                    ->groupBy('revision_round')
                    ->sortKeys();
                $revisionHistories = $submission->statusHistories()->where('to_status', \App\Enums\SubmissionStatus::REVISED)->orderBy('created_at', 'asc')->get();
            @endphp
            
            @if($previousReviews->isNotEmpty())
            <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-4">
                <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Riwayat Revisi Sebelumnya</h2>
                <div class="space-y-6">
                    @foreach($previousReviews as $round => $reviews)
                        <div class="bg-slate-50 border border-border rounded-xl overflow-hidden">
                            <div class="bg-slate-100/50 border-b border-border px-4 py-2.5 flex justify-between items-center">
                                <span class="text-[13px] font-bold text-text">Putaran {{ $round }}</span>
                            </div>
                            <div class="p-4 space-y-4">
                                @foreach($reviews as $prevReview)
                                    <div class="p-4 bg-amber-50/50 border border-amber-200/50 rounded-xl space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[12px] font-bold text-amber-800">Review Anda</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-900 uppercase">
                                                {{ $prevReview->recommendation->label() }}
                                            </span>
                                        </div>
                                        <p class="text-[13px] text-text-secondary whitespace-pre-line leading-relaxed">{{ $prevReview->notes }}</p>
                                        @php
                                            $attachment = $submission->documents->where('uploaded_by', $prevReview->reviewer_id)
                                                ->where('doc_type', 'REVIEW_ATTACHMENT_R' . $round . '_U' . $prevReview->reviewer_id)
                                                ->first();
                                        @endphp
                                        @if($attachment)
                                            <div class="mt-2 text-xs flex items-center gap-1 border-t border-amber-200/50 pt-2">
                                                <span class="text-text-secondary font-medium">Lampiran:</span>
                                                <a href="{{ route('submissions.view-document', ['submission' => $submission->id, 'document' => $attachment->id]) }}" target="_blank" class="text-primary hover:text-primary-hover font-semibold inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    {{ $attachment->original_name }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if(isset($revisionHistories[$round - 1]))
                                    <div class="p-4 bg-blue-50/50 border border-blue-200/50 rounded-xl space-y-2 mt-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[12px] font-bold text-blue-800">Catatan Revisi Mahasiswa</span>
                                            <span class="text-[10px] font-medium text-blue-600">{{ $revisionHistories[$round - 1]->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-[13px] text-text-secondary whitespace-pre-line leading-relaxed">{{ $revisionHistories[$round - 1]->note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card p-6 bg-white border border-border rounded-2xl shadow-sm space-y-6">
                <h2 class="text-xl md:text-[24px] font-semibold text-text border-b border-border pb-2">Form Review (Putaran {{ $currentRound }})</h2>
            
            @if($review && $review->submitted_at)
            <div class="bg-soft-surface rounded-xl p-5 space-y-4 border border-primary/10">
                <p class="text-[14px] font-semibold text-text">Review telah dikirim pada {{ $review->submitted_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</p>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-md text-[12px] font-semibold bg-primary/10 text-primary border border-primary/20">
                        Rekomendasi: {{ $review->recommendation->label() }}
                    </span>
                </div>
                <div class="text-[14px] text-text-secondary bg-white p-4 border border-border rounded-xl whitespace-pre-line leading-relaxed">
                    {{ $review->notes }}
                </div>
                @php
                    $attachment = $submission->documents()
                        ->where('uploaded_by', $review->reviewer_id)
                        ->where('doc_type', 'REVIEW_ATTACHMENT_R' . $review->revision_round . '_U' . $review->reviewer_id)
                        ->first();
                @endphp
                @if($attachment)
                    <div class="mt-2 text-xs flex items-center gap-1">
                        <span class="text-text-secondary font-medium">Lampiran:</span>
                        <a href="{{ route('submissions.view-document', ['submission' => $submission->id, 'document' => $attachment->id]) }}" target="_blank" class="text-primary hover:text-primary-hover font-semibold inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $attachment->original_name }}
                        </a>
                    </div>
                @endif
            </div>
            @else
            <form method="POST" action="{{ route('reviews.store', $submission) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if($errors->any())
                    <x-alert type="error" class="mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                <div class="space-y-2">
                    <label for="notes" class="text-[14px] font-semibold text-text-secondary block">Catatan Review <span class="text-danger">*</span></label>
                    <textarea name="notes" id="notes" rows="6" required class="input-field" placeholder="Tulis catatan review Anda: kekuatan, kelemahan, saran perbaikan...">{{ old('notes', $review?->notes) }}</textarea>
                </div>

                <div class="space-y-2">
                    <label for="attachment" class="text-[14px] font-semibold text-text-secondary block">Lampiran Review (Opsional)</label>
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" class="w-full text-[12px] text-text border border-border rounded-lg bg-white file:mr-3 file:py-1.5 file:px-3 file:border-0 file:text-[12px] file:font-semibold file:bg-slate-100 file:text-text-secondary hover:file:bg-slate-200 cursor-pointer" aria-label="Unggah lampiran review">
                    <p class="text-[11px] text-text-muted mt-1">Format berkas: PDF, Word, Excel, ZIP (Maks. 10MB)</p>
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
                    <button type="button" class="btn-primary" onclick="event.preventDefault(); window.confirmModal('Submit review ini? Anda tidak dapat mengubahnya setelah submit.', this.closest('form'));">
                        Submit Review
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-layouts.app>