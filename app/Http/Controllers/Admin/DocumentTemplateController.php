<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentTemplateController extends Controller
{
    /**
     * Menampilkan daftar semua template dokumen dengan statistik.
     */
    public function index(Request $request)
    {
        $query = DocumentTemplate::query();

        // Search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter
        if ($filter = $request->input('filter')) {
            match ($filter) {
                'active' => $query->where('is_archived', false)->where('is_shown', true),
                'hidden' => $query->where('is_shown', false)->where('is_archived', false),
                'archived' => $query->where('is_archived', true),
                'required' => $query->where('is_required', true)->where('is_archived', false),
                default => null,
            };
        }

        $templates = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => DocumentTemplate::count(),
            'active' => DocumentTemplate::where('is_archived', false)->where('is_shown', true)->count(),
            'hidden' => DocumentTemplate::where('is_shown', false)->where('is_archived', false)->count(),
            'archived' => DocumentTemplate::where('is_archived', true)->count(),
        ];

        return view('admin.document-templates.index', compact('templates', 'stats'));
    }

    /**
     * Pulihkan template dokumen default dari SEMAR
     */
    public function restoreDefault(Request $request)
    {
        $defaults = DocumentTemplate::DEFAULT_TEMPLATES;

        $createdCount = 0;

        DB::transaction(function () use ($defaults, &$createdCount) {
            foreach ($defaults as $default) {
                if (!DocumentTemplate::where('code', $default['code'])->exists()) {
                    DocumentTemplate::create([
                        'name' => $default['name'],
                        'code' => $default['code'],
                        'file_path' => '', // Dummy path since we don't have the file
                        'description' => 'Template dokumen bawaan sistem SEMAR.',
                        'is_required' => true,
                        'is_shown' => true,
                        'is_archived' => false,
                    ]);
                    $createdCount++;
                }
            }

            if ($createdCount > 0) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'submission_id' => null,
                    'old_status' => null,
                    'new_status' => 'TEMPLATE_DEFAULT_RESTORED',
                    'description' => "$createdCount template dokumen bawaan berhasil dipulihkan.",
                ]);
            }
        });

        if ($createdCount > 0) {
            return redirect()->route('admin.templates.index')->with('success', "$createdCount Template berhasil dipulihkan.");
        }

        return redirect()->route('admin.templates.index')->with('success', 'Semua Template Bawaan sudah tersedia.');
    }

    /**
     * Form membuat template baru.
     */
    public function create()
    {
        return view('admin.document-templates.create');
    }

    /**
     * Simpan template baru ke database.
     */
    public function store(Request $request)
    {
        // Normalize code before validation
        if ($request->filled('code')) {
            $normalizedCode = preg_replace('/[^A-Za-z0-9]+/', '_', trim($request->code));
            $normalizedCode = strtoupper(trim(preg_replace('/_+/', '_', $normalizedCode), '_'));
            $request->merge(['code' => $normalizedCode]);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:document_templates,name',
            'code' => 'required|string|max:100|unique:document_templates,code',
            'template_file' => 'required|file|mimes:docx,doc,pdf|max:10240',
            'description' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $path = $request->file('template_file')->store('templates', 'public');

            $template = DocumentTemplate::create([
                'name' => $request->name,
                'code' => $request->code,
                'file_path' => $path,
                'description' => $request->description,
                'is_required' => $request->boolean('is_required'),
                'is_shown' => $request->boolean('is_shown'),
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'TEMPLATE_CREATED',
                'description' => "Template dibuat: {$template->name} (kode: {$template->code})",
            ]);
        });

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template dokumen baru berhasil ditambahkan!');
    }

    /**
     * Form edit template.
     */
    public function edit(DocumentTemplate $template)
    {
        $usageCount = $template->documents()->count();
        $submissionCount = $template->submissionCount();

        return view('admin.document-templates.edit', compact('template', 'usageCount', 'submissionCount'));
    }

    /**
     * Update data template (code immutable).
     */
    public function update(Request $request, DocumentTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:document_templates,name,' . $template->id,
            'template_file' => 'nullable|file|mimes:docx,doc,pdf|max:10240',
            'description' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $template) {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'is_required' => $request->boolean('is_required'),
                'is_shown' => $request->boolean('is_shown'),
            ];

            if ($request->hasFile('template_file')) {
                if (Storage::disk('public')->exists($template->file_path)) {
                    Storage::disk('public')->delete($template->file_path);
                }
                $data['file_path'] = $request->file('template_file')->store('templates', 'public');
            }

            $template->update($data);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'TEMPLATE_UPDATED',
                'description' => "Template diperbarui: {$template->name}",
            ]);
        });

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template dokumen berhasil diperbarui!');
    }

    /**
     * Arsipkan template (soft archive, no hard delete).
     */
    public function archive(DocumentTemplate $template)
    {
        DB::transaction(function () use ($template) {
            $template->update([
                'is_archived' => true,
                'is_shown' => false,
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'TEMPLATE_ARCHIVED',
                'description' => "Template diarsipkan: {$template->name}",
            ]);
        });

        return redirect()->route('admin.templates.index')
            ->with('success', "Template '{$template->name}' berhasil diarsipkan.");
    }

    /**
     * Pulihkan template dari arsip.
     */
    public function restore(DocumentTemplate $template)
    {
        DB::transaction(function () use ($template) {
            $template->update([
                'is_archived' => false,
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'TEMPLATE_RESTORED',
                'description' => "Template dipulihkan: {$template->name}",
            ]);
        });

        return redirect()->route('admin.templates.index')
            ->with('success', "Template '{$template->name}' berhasil dipulihkan.");
    }

    /**
     * Toggle required status.
     */
    public function toggleRequired(DocumentTemplate $template)
    {
        $newStatus = !$template->is_required;

        DB::transaction(function () use ($template, $newStatus) {
            $template->update(['is_required' => $newStatus]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'TEMPLATE_REQUIRED_CHANGED',
                'description' => "Template status wajib diubah: {$template->name} → " . ($newStatus ? 'Wajib' : 'Opsional'),
            ]);
        });

        return redirect()->route('admin.templates.index')
            ->with('success', "Status wajib template '{$template->name}' diubah menjadi " . ($newStatus ? 'Wajib' : 'Opsional') . '.');
    }

    /**
     * Toggle visibility.
     */
    public function toggleShown(DocumentTemplate $template)
    {
        $newStatus = !$template->is_shown;

        DB::transaction(function () use ($template, $newStatus) {
            $template->update(['is_shown' => $newStatus]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'submission_id' => null,
                'old_status' => null,
                'new_status' => 'TEMPLATE_VISIBILITY_CHANGED',
                'description' => "Template visibilitas diubah: {$template->name} → " . ($newStatus ? 'Tampil' : 'Sembunyikan'),
            ]);
        });

        return redirect()->route('admin.templates.index')
            ->with('success', "Visibilitas template '{$template->name}' diubah menjadi " . ($newStatus ? 'Tampil' : 'Sembunyikan') . '.');
    }
}
