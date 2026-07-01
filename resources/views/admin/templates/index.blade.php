<x-layouts.app :title="'Manajemen Template Dokumen'">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-text">Manajemen Template Dokumen</h2>
            <p class="text-sm text-text-secondary mt-1">Kelola data berkas, ketentuan wajib pengumpulan, dan visibilitas dokumen bagi mahasiswa.</p>
        </div>
        <button onclick="openModal('addModal')" class="btn-primary py-2 px-4 text-sm font-semibold flex items-center gap-2 rounded-xl shadow-sm self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Template
        </button>
    </div>



    {{-- Table List Content --}}
    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-soft-surface text-xs font-bold text-text-muted uppercase tracking-wider border-b border-border">
                        <th class="px-6 py-4">Nama Template</th>
                        <th class="px-6 py-4">Ukuran File</th>
                        <th class="px-6 py-4">Ketentuan Pengisian</th>
                        <th class="px-6 py-4">Status Tampil</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-sm text-text">
                    @forelse($templates as $item)
                        <tr class="hover:bg-soft-surface/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-text">{{ $item->name }}</div>
                                <div class="text-xs text-text-muted mt-0.5 max-w-sm truncate">{{ $item->description ?? 'Tidak ada deskripsi' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-text-secondary">
                                {{ $item->file_size }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->is_required)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Wajib Diunggah</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">Opsional (Sifat Pendukung)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item->is_shown)
                                    <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium text-xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Diperlihatkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-text-muted font-medium text-xs">
                                        <span class="w-2 h-2 rounded-full bg-text-muted/50"></span> Disembunyikan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="openEditModal({{ json_encode($item) }})" class="p-1.5 text-text-secondary hover:text-primary rounded-lg hover:bg-soft-surface transition-colors" title="Edit Template">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.templates.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-text-muted hover:text-danger rounded-lg hover:bg-danger-bg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-muted italic">Belum ada data template master yang tersimpan di sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL ADD TEMPLATE --}}
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-border w-full max-w-lg shadow-xl overflow-hidden p-6">
            <h3 class="text-base font-bold text-text mb-4">Tambah Template Baru</h3>
            <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-text-muted mb-2">Nama Template</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm" placeholder="Contoh: Format Protokol Etik">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-text-muted mb-2">Deskripsi Keterangan</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm" placeholder="Tulis catatan opsional perihal file dokumen terkait..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-text-muted mb-2">Berkas Master File (.docx / .pdf)</label>
                    <input type="file" name="template_file" required accept=".docx,.doc,.pdf" class="w-full text-sm text-text border border-border bg-soft-surface file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 rounded-xl">
                </div>
                
                {{-- Pilihan Checkbox --}}
                <div class="bg-soft-surface/50 p-3 rounded-xl border border-border space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_required" value="1" checked class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                        <div>
                            <span class="text-sm font-semibold text-text block leading-tight">Wajib Diisi oleh Mahasiswa</span>
                            <span class="text-xs text-text-muted">Jika dicentang, mahasiswa tidak bisa mengirim pengajuan sebelum mengunggah file ini.</span>
                        </div>
                    </label>
                    <hr class="border-border">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_shown" value="1" checked class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                        <div>
                            <span class="text-sm font-semibold text-text block leading-tight">Perlihatkan Template</span>
                            <span class="text-xs text-text-muted">Tampilkan berkas master unduhan di halaman berkas mahasiswa.</span>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-border mt-6">
                    <button type="button" onclick="closeModal('addModal')" class="btn-secondary px-4 py-2 text-sm font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="btn-primary px-4 py-2 text-sm font-semibold rounded-xl shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT TEMPLATE --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-border w-full max-w-lg shadow-xl overflow-hidden p-6">
            <h3 class="text-base font-bold text-text mb-4">Edit Data Template</h3>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase text-text-muted mb-2">Nama Template</label>
                    <input type="text" name="name" id="edit_name" required class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-text-muted mb-2">Deskripsi Keterangan</label>
                    <textarea name="description" id="edit_description" rows="2" class="w-full px-4 py-2.5 bg-soft-surface border border-border rounded-xl focus:outline-none focus:border-primary text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-text-muted mb-2">Perbarui Berkas Berkas (Kosongkan jika tidak diganti)</label>
                    <input type="file" name="template_file" accept=".docx,.doc,.pdf" class="w-full text-sm text-text border border-border bg-soft-surface file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 rounded-xl">
                </div>
                
                <div class="bg-soft-surface/50 p-3 rounded-xl border border-border space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_required" id="edit_is_required" value="1" class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                        <div>
                            <span class="text-sm font-semibold text-text block leading-tight">Wajib Diisi oleh Mahasiswa</span>
                        </div>
                    </label>
                    <hr class="border-border">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_shown" id="edit_is_shown" value="1" class="mt-0.5 rounded border-border text-primary focus:ring-primary">
                        <div>
                            <span class="text-sm font-semibold text-text block leading-tight">Perlihatkan Template</span>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-border mt-6">
                    <button type="button" onclick="closeModal('editModal')" class="btn-secondary px-4 py-2 text-sm font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="btn-primary px-4 py-2 text-sm font-semibold rounded-xl shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        function openEditModal(item) {
            document.getElementById('editForm').action = `/admin/templates/${item.id}`;
            document.getElementById('edit_name').value = item.name;
            document.getElementById('edit_description').value = item.description || '';
            document.getElementById('edit_is_required').checked = !!item.is_required;
            document.getElementById('edit_is_shown').checked = !!item.is_shown;
            openModal('editModal');
        }
    </script>
</x-layouts.app>