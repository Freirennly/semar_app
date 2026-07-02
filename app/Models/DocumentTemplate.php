<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DocumentTemplate extends Model
{
    use HasFactory;

    public const DEFAULT_TEMPLATES = [
        ['code' => 'PROPOSAL_PENELITIAN', 'name' => 'Proposal Penelitian'],
        ['code' => 'SURAT_PENGANTAR', 'name' => 'Surat Pengantar'],
        ['code' => 'SURAT_IZIN_PENELITIAN', 'name' => 'Surat Izin Penelitian'],
    ];

    protected $fillable = [
        'name',
        'code',
        'file_path',
        'description',
        'is_required',
        'is_shown',
        'is_archived',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_shown' => 'boolean',
        'is_archived' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (DocumentTemplate $template) {
            // Normalize code to UPPER_SNAKE_CASE
            if (!empty($template->code)) {
                // Strip non-alphanumeric, collapse underscores, uppercase
                $code = preg_replace('/[^A-Za-z0-9]+/', '_', trim($template->code));
                $code = preg_replace('/_+/', '_', $code);
                $template->code = strtoupper(trim($code, '_'));
            } else {
                $code = preg_replace('/[^A-Za-z0-9]+/', '_', trim($template->name));
                $code = preg_replace('/_+/', '_', $code);
                $template->code = strtoupper(trim($code, '_'));
                if (empty($template->code)) {
                    $template->code = 'TEMP_' . Str::upper(Str::random(6));
                }
            }

            // Ensure uniqueness
            $originalCode = $template->code;
            $counter = 1;
            while (static::where('code', $template->code)->exists()) {
                $template->code = $originalCode . '_' . $counter;
                $counter++;
            }
        });

        static::saving(function (DocumentTemplate $template) {
            // Prevent code modification on existing records
            if ($template->exists && $template->isDirty('code')) {
                throw ValidationException::withMessages([
                    'code' => 'Kode template tidak dapat diubah setelah dibuat.',
                ]);
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget(\App\Services\DocumentIntegrityService::CACHE_KEY);
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget(\App\Services\DocumentIntegrityService::CACHE_KEY);
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    /**
     * Documents uploaded by students using this template.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(SubmissionDocument::class, 'document_template_id');
    }

    // ─── Query Helpers ────────────────────────────────────────────

    /**
     * Count of distinct submissions that reference this template.
     */
    public function submissionCount(): int
    {
        return $this->documents()->distinct('submission_id')->count('submission_id');
    }

    /**
     * Whether this template is referenced by any uploaded document.
     */
    public function isUsed(): bool
    {
        return $this->documents()->exists();
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_shown', true)->where('is_archived', false);
    }

    // ─── Accessors ────────────────────────────────────────────────

    /**
     * Helper untuk mengambil ukuran file
     */
    public function getFileSizeAttribute()
    {
        if (!empty($this->file_path) && Storage::disk('public')->exists($this->file_path)) {
            try {
                $bytes = Storage::disk('public')->size($this->file_path);
                if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
                if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
                return $bytes . ' bita';
            } catch (\Exception $e) {
                return '-';
            }
        }
        return '-';
    }
}