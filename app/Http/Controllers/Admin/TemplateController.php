<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    /**
     * Menampilkan semua daftar template dokumen di dashboard admin.
     */
    public function index()
    {
        // Menarik data template terbaru dari database
        $templates = DocumentTemplate::latest()->get();
        
        // Mengirimkan variabel $templates ke view admin
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Menyimpan template dokumen baru ke storage dan database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:docx,doc,pdf|max:10240',
            'description' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('template_file')) {
            $path = $request->file('template_file')->store('templates', 'public');

            DocumentTemplate::create([
                'name' => $request->name,
                'file_path' => $path,
                'description' => $request->description,
                'is_required' => $request->has('is_required'),
                'is_shown' => $request->has('is_shown'),
            ]);

            return redirect()->route('admin.templates.index')->with('success', 'Template dokumen baru berhasil ditambahkan!');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah berkas.');
    }

    /**
     * Memperbarui data atau file template dokumen yang sudah ada.
     */
    public function update(Request $request, DocumentTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template_file' => 'nullable|file|mimes:docx,doc,pdf|max:10240',
            'description' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_required' => $request->has('is_required'),
            'is_shown' => $request->has('is_shown'),
        ];

        if ($request->hasFile('template_file')) {
            // Hapus file fisik lama di storage jika ada
            if (Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }
            $data['file_path'] = $request->file('template_file')->store('templates', 'public');
        }

        $template->update($data);

        return redirect()->route('admin.templates.index')->with('success', 'Template dokumen berhasil diperbarui!');
    }

    /**
     * Menghapus data dan file fisik template dari sistem.
     */
    public function destroy(DocumentTemplate $template)
    {
        if (Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        return redirect()->route('admin.templates.index')->with('success', 'Template dokumen berhasil dihapus dari sistem.');
    }
}