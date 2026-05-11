<x-layouts.app :title="'Buat Pengajuan'">
    <div class="mb-6">
        <nav class="text-sm text-text-muted mb-2" aria-label="Breadcrumb"><a href="{{ route('submissions.index') }}" class="hover:text-primary">Pengajuan</a> <span class="mx-1">/</span> <span class="text-text">Buat Baru</span></nav>
        <h2 class="text-xl font-bold text-text">Buat Pengajuan Baru</h2>
    </div>
    <div class="card p-6 max-w-2xl">
        @if($errors->any())
        <div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('submissions.store') }}">
            @csrf
            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-text mb-1.5">Judul Pengajuan <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="input-field" placeholder="Contoh: Penelitian Dampak Kebijakan Lingkungan">
            </div>
            <div class="mb-5">
                <label for="type" class="block text-sm font-medium text-text mb-1.5">Jenis Pengajuan <span class="text-danger">*</span></label>
                <select name="type" id="type" required class="input-field">
                    <option value="">Pilih jenis...</option>
                    <option value="Penelitian" {{ old('type') == 'Penelitian' ? 'selected' : '' }}>Penelitian</option>
                    <option value="Pengabdian" {{ old('type') == 'Pengabdian' ? 'selected' : '' }}>Pengabdian</option>
                    <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="mb-6">
                <label for="abstract" class="block text-sm font-medium text-text mb-1.5">Abstrak / Ringkasan</label>
                <textarea name="abstract" id="abstract" rows="4" class="input-field" placeholder="Jelaskan ringkasan pengajuan Anda...">{{ old('abstract') }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">Simpan Draft</button>
                <a href="{{ route('submissions.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
