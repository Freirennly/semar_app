<x-layouts.app :title="'Jadwalkan Fullboard: ' . $submission->title">

<div class="mb-8">
    <nav class="flex items-center gap-1.5 mb-4 text-xs font-medium" style="color:#8E8CAD">
        <a href="{{ route('decisions.index') }}" class="transition-colors duration-150 hover:underline" style="color:#463EE3">Keputusan</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('decisions.show', $submission) }}" class="transition-colors duration-150 hover:underline" style="color:#463EE3">{{ $submission->code }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color:#5A587A">Jadwal Fullboard</span>
    </nav>

    <div class="flex items-center gap-2 mb-1.5">
        <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
        <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Sidang Fullboard</p>
    </div>
    <h1 class="text-2xl font-bold tracking-tight leading-tight mb-2" style="color:#0F0E2E">
        Jadwalkan Pertemuan
    </h1>
    <div class="flex flex-wrap items-center gap-3">
        <span class="font-mono text-[11px] px-2 py-1 rounded-lg"
              style="background:#F5F5F5; color:#5A587A; border:1px solid rgba(0,0,0,0.06);">
            {{ $submission->code }}
        </span>
        <span class="text-sm font-light" style="color:#5A587A">{{ $submission->title }}</span>
    </div>
</div>

<div class="bg-white rounded-2xl border overflow-hidden max-w-3xl" style="border-color:rgba(70,62,227,0.14); box-shadow: 0 2px 16px rgba(70,62,227,0.07);">
    <div class="flex items-center gap-2.5 px-6 py-5" style="border-bottom:1.5px solid rgba(70,62,227,0.08); background:rgba(70,62,227,0.025);">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:#E6E6FA; box-shadow:0 1px 4px rgba(70,62,227,0.15);">
            <svg class="w-4 h-4" fill="none" stroke="#463EE3" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="text-base font-bold" style="color:#0F0E2E">Formulir Penjadwalan</h3>
    </div>

    <div class="p-6">
        @if($errors->any())
            <x-alert type="error" class="mb-6">
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
            </x-alert>
        @endif

        <form method="POST" action="{{ route('fullboard.store', $submission) }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="scheduled_date" class="block text-xs font-bold mb-1.5" style="color:#5A587A">Tanggal <span style="color:#EF4444">*</span></label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required
                           class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 font-light"
                           style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                           onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                           onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">
                </div>
                <div>
                    <label for="start_time" class="block text-xs font-bold mb-1.5" style="color:#5A587A">Jam Mulai <span style="color:#EF4444">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                           class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 font-light"
                           style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                           onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                           onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">
                </div>
                <div>
                    <label for="end_time" class="block text-xs font-bold mb-1.5" style="color:#5A587A">Jam Selesai <span class="font-normal opacity-70">(Opsional)</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}"
                           class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 font-light"
                           style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                           onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                           onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold mb-1.5" style="color:#5A587A">Jenis Rapat <span style="color:#EF4444">*</span></label>
                <div class="flex gap-4 items-center">
                    <label class="flex items-center gap-2 text-sm font-light" style="color:#0F0E2E;">
                        <input type="radio" name="meeting_type" value="OFFLINE" {{ old('meeting_type', 'OFFLINE') == 'OFFLINE' ? 'checked' : '' }} class="w-4 h-4 text-primary bg-gray-100 border-gray-300 focus:ring-primary">
                        Offline
                    </label>
                    <label class="flex items-center gap-2 text-sm font-light" style="color:#0F0E2E;">
                        <input type="radio" name="meeting_type" value="ONLINE" {{ old('meeting_type') == 'ONLINE' ? 'checked' : '' }} class="w-4 h-4 text-primary bg-gray-100 border-gray-300 focus:ring-primary">
                        Online
                    </label>
                    <label class="flex items-center gap-2 text-sm font-light" style="color:#0F0E2E;">
                        <input type="radio" name="meeting_type" value="HYBRID" {{ old('meeting_type') == 'HYBRID' ? 'checked' : '' }} class="w-4 h-4 text-primary bg-gray-100 border-gray-300 focus:ring-primary">
                        Hybrid
                    </label>
                </div>
            </div>

            <div>
                <label for="location" class="block text-xs font-bold mb-1.5" style="color:#5A587A">Lokasi <span class="font-normal opacity-70">(Opsional jika ada Link Meeting)</span></label>
                <input type="text" name="location" id="location" value="{{ old('location') }}"
                       class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 font-light"
                       style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                       placeholder="Contoh: Ruang Sidang Lt. 2"
                       onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                       onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">
            </div>

            <div>
                <label for="meeting_url" class="block text-xs font-bold mb-1.5" style="color:#5A587A">Link Meeting <span class="font-normal opacity-70">(Opsional jika ada Lokasi)</span></label>
                <input type="url" name="meeting_url" id="meeting_url" value="{{ old('meeting_url') }}"
                       class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 font-light"
                       style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                       placeholder="https://zoom.us/j/..."
                       onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                       onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">
            </div>

            <div>
                <label for="agenda" class="block text-xs font-bold mb-1.5" style="color:#5A587A">Agenda <span style="color:#EF4444">*</span></label>
                <textarea name="agenda" id="agenda" rows="3" required
                          class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 resize-y font-light"
                          style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                          placeholder="Agenda pembahasan sidang..."
                          onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                          onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">{{ old('agenda', 'Pembahasan Proposal: ' . $submission->title) }}</textarea>
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold mb-1.5" style="color:#5A587A">Catatan Tambahan <span class="font-normal opacity-70">(Opsional)</span></label>
                <textarea name="notes" id="notes" rows="2"
                          class="w-full text-sm rounded-xl border px-3.5 py-2.5 outline-none transition-all duration-150 resize-y font-light"
                          style="border-color:rgba(70,62,227,0.18); color:#0F0E2E; background:#fafafa;"
                          placeholder="Catatan untuk peserta sidang..."
                          onfocus="this.style.borderColor='#463EE3'; this.style.boxShadow='0 0 0 3px rgba(70,62,227,0.1)'; this.style.background='white'"
                          onblur="this.style.borderColor='rgba(70,62,227,0.18)'; this.style.boxShadow='none'; this.style.background='#fafafa'">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3" style="border-top:1px solid rgba(70,62,227,0.08);">
                <a href="{{ route('decisions.show', $submission) }}" class="px-5 py-2.5 rounded-xl text-sm font-bold border transition-all duration-150"
                   style="border-color:rgba(0,0,0,0.1); color:#5A587A; background:white;"
                   onmouseover="this.style.background='#F5F5F5'" onmouseout="this.style.background='white'">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-150 flex items-center gap-2"
                        style="background:#463EE3; color:white; box-shadow:0 2px 8px rgba(70,62,227,0.28);"
                        onmouseover="this.style.background='#332DB8'; this.style.boxShadow='0 5px 16px rgba(70,62,227,0.35)'"
                        onmouseout="this.style.background='#463EE3'; this.style.boxShadow='0 2px 8px rgba(70,62,227,0.28)'">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

</x-layouts.app>
