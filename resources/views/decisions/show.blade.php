<x-layouts.app :title="'Keputusan: ' . $submission->title">

{{-- ═══════════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════════════ --}}
<div class="mb-8">
    <nav class="flex items-center gap-1.5 mb-4 text-xs font-medium" style="color:#8E8CAD">
        <a href="{{ route('decisions.index') }}"
           class="transition-colors duration-150 hover:underline" style="color:#463EE3">Keputusan</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color:#5A587A">{{ $submission->code }}</span>
    </nav>

    <div class="flex flex-wrap items-start gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1.5">
                <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
                <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Keputusan Akhir</p>
            </div>
            <h1 class="text-2xl font-bold tracking-tight leading-tight mb-2" style="color:#0F0E2E">
                {{ $submission->title }}
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <span class="font-mono text-[11px] px-2 py-1 rounded-lg"
                      style="background:#F5F5F5; color:#5A587A; border:1px solid rgba(0,0,0,0.06);">
                    {{ $submission->code }}
                </span>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold"
                         style="background:#F5F5F5; color:#5A587A;">
                        {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                    </div>
                    <span class="text-sm font-light" style="color:#5A587A">{{ $submission->student->name }}</span>
                </div>
                <x-status-badge :status="$submission->status" />
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     MAIN GRID
════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Hasil Review (left 2/3) ─────────────────── --}}
    <div class="lg:col-span-2 flex flex-col gap-4">

        {{-- Section label --}}
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:#E6E6FA; box-shadow:0 1px 4px rgba(70,62,227,0.15);">
                <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold" style="color:#0F0E2E">Hasil Review</h3>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                  style="background:#E6E6FA; color:#463EE3;">
                {{ $submission->reviews->count() }} review
            </span>
        </div>

        {{-- Review cards --}}
        @forelse($submission->reviews as $index => $rev)
        @php
            $recColor = match($rev->recommendation?->value ?? '') {
                'APPROVED'      => ['bg'=>'rgba(34,197,94,0.1)',  'text'=>'#15803d', 'border'=>'rgba(34,197,94,0.2)'],
                'RESUBMISSION'  => ['bg'=>'rgba(245,158,11,0.1)', 'text'=>'#b45309', 'border'=>'rgba(245,158,11,0.22)'],
                'DISAPPROVED'   => ['bg'=>'rgba(239,68,68,0.1)',  'text'=>'#b91c1c', 'border'=>'rgba(239,68,68,0.2)'],
                default         => ['bg'=>'#F5F5F5',              'text'=>'#5A587A', 'border'=>'rgba(0,0,0,0.08)'],
            };
        @endphp
        <div class="bg-white rounded-2xl border overflow-hidden transition-all duration-200"
             style="border-color:rgba(70,62,227,0.12);
                    box-shadow: 0 2px 12px rgba(70,62,227,0.06);">

            {{-- Reviewer header --}}
            <div class="flex items-center justify-between px-5 py-4"
                 style="border-bottom:1px solid rgba(70,62,227,0.07);
                        background:rgba(70,62,227,0.02);">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0"
                         style="background:#E6E6FA; color:#463EE3;">
                        {{ strtoupper(substr($rev->reviewer->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold" style="color:#0F0E2E">{{ $rev->reviewer->name }}</p>
                        <p class="text-xs font-light mt-0.5" style="color:#8E8CAD">
                            {{ $rev->submitted_at?->format('d M Y, H:i') ?? 'Belum submit' }}
                        </p>
                    </div>
                </div>

                @if($rev->recommendation)
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg border"
                      style="background:{{ $recColor['bg'] }}; color:{{ $recColor['text'] }}; border-color:{{ $recColor['border'] }};">
                    {{ $rev->recommendation->label() }}
                </span>
                @else
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg"
                      style="background:#F5F5F5; color:#8E8CAD; border:1px solid rgba(0,0,0,0.07);">
                    Menunggu
                </span>
                @endif
            </div>

            {{-- Review notes --}}
            <div class="px-5 py-4">
                @if($rev->notes)
                <p class="text-sm font-light leading-relaxed" style="color:#5A587A">{{ $rev->notes }}</p>
                @else
                <p class="text-sm font-light italic" style="color:#b0aec8">Belum ada catatan.</p>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border flex flex-col items-center justify-center py-14 text-center"
             style="border-color:rgba(70,62,227,0.1); border-style:dashed;">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3"
                 style="background:#F5F5F5;">
                <svg class="w-6 h-6" fill="none" stroke="#c4c2e0" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold" style="color:#8E8CAD">Belum ada review</p>
            <p class="text-xs font-light mt-1" style="color:#b0aec8">Reviewer belum mengirimkan hasil telaah</p>
        </div>
        @endforelse
    </div>

    {{-- ── Keputusan Akhir (right 1/3) ─────────────── --}}
    <div>
        @if($submission->latestDecision)
        {{-- ── Keputusan sudah ada ─── --}}
        @php
            $dec = $submission->latestDecision;
            $decColor = match($dec->decision->value ?? '') {
                'APPROVED'     => ['bg'=>'rgba(34,197,94,0.1)',  'text'=>'#15803d', 'border'=>'rgba(34,197,94,0.25)',  'panelBorder'=>'rgba(34,197,94,0.2)',  'panelShadow'=>'rgba(34,197,94,0.08)'],
                'RESUBMISSION' => ['bg'=>'rgba(245,158,11,0.1)', 'text'=>'#b45309', 'border'=>'rgba(245,158,11,0.28)', 'panelBorder'=>'rgba(245,158,11,0.22)', 'panelShadow'=>'rgba(245,158,11,0.08)'],
                'DISAPPROVED'  => ['bg'=>'rgba(239,68,68,0.1)',  'text'=>'#b91c1c', 'border'=>'rgba(239,68,68,0.22)',  'panelBorder'=>'rgba(239,68,68,0.2)',  'panelShadow'=>'rgba(239,68,68,0.08)'],
                default        => ['bg'=>'#F5F5F5',              'text'=>'#5A587A', 'border'=>'rgba(0,0,0,0.08)',      'panelBorder'=>'rgba(0,0,0,0.1)',      'panelShadow'=>'rgba(0,0,0,0.05)'],
            };
        @endphp
        <div class="bg-white rounded-2xl border overflow-hidden"
             style="border-color:{{ $decColor['panelBorder'] }};
                    box-shadow: 0 2px 16px {{ $decColor['panelShadow'] }};">
            <div class="px-5 py-4 flex items-center gap-2.5"
                 style="border-bottom:1px solid {{ $decColor['panelBorder'] }};
                        background:{{ $decColor['bg'] }};">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:white; border:1px solid {{ $decColor['border'] }};">
                    <svg class="w-4 h-4" fill="none" stroke="{{ $decColor['text'] }}" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold tracking-widest uppercase" style="color:{{ $decColor['text'] }}">Keputusan Final</p>
                    <p class="text-xs font-light" style="color:#5A587A">Sudah diputuskan</p>
                </div>
            </div>
            <div class="px-5 py-5">
                <span class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-lg border mb-4"
                      style="background:{{ $decColor['bg'] }}; color:{{ $decColor['text'] }}; border-color:{{ $decColor['border'] }};">
                    {{ $dec->decision->label() }}
                </span>
                @if($dec->notes)
                <p class="text-sm font-light leading-relaxed" style="color:#5A587A">{{ $dec->notes }}</p>
                @endif
            </div>
        </div>

        @else
        {{-- ── Form keputusan ─── --}}
        <div class="bg-white rounded-2xl border overflow-hidden"
             style="border-color:rgba(70,62,227,0.14);
                    box-shadow: 0 2px 16px rgba(70,62,227,0.07);">

            <div class="flex items-center gap-2.5 px-5 py-4"
                 style="border-bottom:1.5px solid rgba(70,62,227,0.08);
                        background:rgba(70,62,227,0.025);">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                     style="background:#E6E6FA; box-shadow:0 1px 4px rgba(70,62,227,0.15);">
                    <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold" style="color:#0F0E2E">Keputusan Akhir</h3>
            </div>

            <div class="p-5">

                {{-- Validation errors --}}
                @if($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl border text-sm"
                     style="background:rgba(239,68,68,0.07); border-color:rgba(239,68,68,0.2); color:#b91c1c;">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $e)
                        <li class="flex items-center gap-2 text-xs font-medium">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/>
                            </svg>
                            {{ $e }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('decisions.store', $submission) }}" class="space-y-5">
                    @csrf

                    {{-- Radio options --}}
                    <div>
                        <p class="text-xs font-bold mb-2.5" style="color:#5A587A">
                            Keputusan <span style="color:#EF4444">*</span>
                        </p>
                        <div class="space-y-2">

                            {{-- Approved --}}
                            <label class="flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition-all duration-150 group"
                                   style="border-color:rgba(70,62,227,0.12); background:white;"
                                   onmouseover="this.style.background='rgba(34,197,94,0.04)'; this.style.borderColor='rgba(34,197,94,0.3)'"
                                   onmouseout="if(!this.querySelector('input').checked){this.style.background='white'; this.style.borderColor='rgba(70,62,227,0.12)'}">
                                <input type="radio" name="decision" value="APPROVED" required
                                       class="w-4 h-4 accent-[#22C55E] cursor-pointer"
                                       onchange="document.querySelectorAll('.decision-radio-label').forEach(el=>{ el.style.background='white'; el.style.borderColor='rgba(70,62,227,0.12)' }); this.closest('label').style.background='rgba(34,197,94,0.06)'; this.closest('label').style.borderColor='rgba(34,197,94,0.35)'">
                                <div class="flex items-center gap-2.5 flex-1">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0"
                                         style="background:rgba(34,197,94,0.12);">
                                        <svg class="w-3 h-3" fill="none" stroke="#15803d" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold" style="color:#0F0E2E">Disetujui</p>
                                        <p class="text-[10px] font-light" style="color:#8E8CAD">Approved</p>
                                    </div>
                                </div>
                            </label>

                            {{-- Resubmission --}}
                            <label class="decision-radio-label flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition-all duration-150"
                                   style="border-color:rgba(70,62,227,0.12); background:white;"
                                   onmouseover="this.style.background='rgba(245,158,11,0.04)'; this.style.borderColor='rgba(245,158,11,0.3)'"
                                   onmouseout="if(!this.querySelector('input').checked){this.style.background='white'; this.style.borderColor='rgba(70,62,227,0.12)'}">
                                <input type="radio" name="decision" value="RESUBMISSION"
                                       class="w-4 h-4 accent-[#F59E0B] cursor-pointer"
                                       onchange="document.querySelectorAll('.decision-radio-label').forEach(el=>{ el.style.background='white'; el.style.borderColor='rgba(70,62,227,0.12)' }); this.closest('label').style.background='rgba(245,158,11,0.06)'; this.closest('label').style.borderColor='rgba(245,158,11,0.35)'">
                                <div class="flex items-center gap-2.5 flex-1">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0"
                                         style="background:rgba(245,158,11,0.12);">
                                        <svg class="w-3 h-3" fill="none" stroke="#b45309" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold" style="color:#0F0E2E">Perlu Revisi</p>
                                        <p class="text-[10px] font-light" style="color:#8E8CAD">Resubmission</p>
                                    </div>
                                </div>
                            </label>

                            {{-- Disapproved --}}
                            <label class="decision-radio-label flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition-all duration-150"
                                   style="border-color:rgba(70,62,227,0.12); background:white;"
                                   onmouseover="this.style.background='rgba(239,68,68,0.04)'; this.style.borderColor='rgba(239,68,68,0.3)'"
                                   onmouseout="if(!this.querySelector('input').checked){this.style.background='white'; this.style.borderColor='rgba(70,62,227,0.12)'}">
                                <input type="radio" name="decision" value="DISAPPROVED"
                                       class="w-4 h-4 accent-[#EF4444] cursor-pointer"
                                       onchange="document.querySelectorAll('.decision-radio-label').forEach(el=>{ el.style.background='white'; el.style.borderColor='rgba(70,62,227,0.12)' }); this.closest('label').style.background='rgba(239,68,68,0.06)'; this.closest('label').style.borderColor='rgba(239,68,68,0.35)'">
                                <div class="flex items-center gap-2.5 flex-1">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0"
                                         style="background:rgba(239,68,68,0.1);">
                                        <svg class="w-3 h-3" fill="none" stroke="#b91c1c" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold" style="color:#0F0E2E">Ditolak</p>
                                        <p class="text-[10px] font-light" style="color:#8E8CAD">Disapproved</p>
                                    </div>
                                </div>
                            </label>

                        </div>
                    </div>

                    {{-- Notes textarea --}}
                    <div>
                        <label for="notes" class="block text-xs font-bold mb-1.5" style="color:#5A587A">
                            Catatan
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 resize-none font-light"
                                  style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                                  placeholder="Alasan atau catatan keputusan..."
                                  onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                                  onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl text-sm font-bold transition-all duration-150 flex items-center justify-center gap-2"
                            style="background:#463EE3; color:white; box-shadow:0 2px 8px rgba(70,62,227,0.28);"
                            onmouseover="this.style.background='#332DB8'; this.style.boxShadow='0 5px 16px rgba(70,62,227,0.35)'"
                            onmouseout="this.style.background='#463EE3'; this.style.boxShadow='0 2px 8px rgba(70,62,227,0.28)'"
                            onclick="return confirm('Simpan keputusan akhir? Tindakan ini tidak dapat dibatalkan.')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Keputusan
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

</div>

</x-layouts.app>