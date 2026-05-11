<x-layouts.app :title="'Edit Pengajuan'">
    <div class="mb-6">
        <nav class="text-sm text-text-muted mb-2" aria-label="Breadcrumb"><a href="{{ route('submissions.index') }}" class="hover:text-primary">Pengajuan</a> <span class="mx-1">/</span> <a href="{{ route('submissions.show', $submission) }}" class="hover:text-primary">{{ $submission->code }}</a> <span class="mx-1">/</span> <span class="text-text">Edit</span></nav>
        <h2 class="text-xl font-bold text-text">Edit Pengajuan</h2>
    </div>
    <div class="card p-6 max-w-2xl">
        @if($errors->any())
        <div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('submissions.update', $submission) }}">
            @csrf @method('PUT')
            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-text mb-1.5">Judul Pengajuan</label>
                <input type="text" name="title" id="title" value="{{ old('title', $submission->title) }}" required class="input-field">
            </div>
            <div class="mb-5">
                <label for="type" class="block text-sm font-medium text-text mb-1.5">Jenis</label>
                <select name="type" id="type" required class="input-field">
                    @foreach(['Penelitian', 'Pengabdian', 'Lainnya'] as $t)
                    <option value="{{ $t }}" {{ old('type', $submission->type) == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label for="abstract" class="block text-sm font-medium text-text mb-1.5">Abstrak</label>
                <textarea name="abstract" id="abstract" rows="4" class="input-field">{{ old('abstract', $submission->abstract) }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('submissions.show', $submission) }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
