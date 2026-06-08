<?php

namespace App\Models;

use App\Enums\DocType;
use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'code', 'title', 'type', 'status', 'student_id',
        'abstract', 'submitted_at', 'decided_at', 'secretary_id'
    ];

    protected $casts = [
        'status' => SubmissionStatus::class,
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    /**
     * Hook Eloquent untuk mengisi otomatis kode pengajuan sebelum data masuk ke DB
     */
    protected static function booted()
    {
        static::creating(function ($submission) {
            if (empty($submission->code)) {
                $submission->code = static::generateCode();
            }
        });
    }

    /**
     * Menggenerasikan kode registrasi unik berbasis tahun berjalan (SUB-2026-0001)
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        $last = static::where('code', 'like', "SUB-{$year}-%")->max('code');
        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return sprintf('SUB-%s-%04d', $year, $seq);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SubmissionDocument::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class)->orderByDesc('created_at');
    }

    public function latestDecision()
    {
        return $this->hasOne(Decision::class)->latestOfMany();
    }

    /**
     * PERBAIKAN: Memeriksa apakah seluruh dokumen WAJIB dari template DB sudah terpenuhi
     */
    public function hasAllDocuments(): bool
    {
        $requiredTemplateIds = DocumentTemplate::where('is_shown', true)
            ->where('is_required', true)
            ->pluck('id');

        $uploadedTemplateIds = $this->documents()->pluck('document_template_id');

        return $requiredTemplateIds->diff($uploadedTemplateIds)->isEmpty();
    }

    public function getDocumentCount(): int
    {
        return $this->documents()->count();
    }

    /**
     * PERBAIKAN: Menghitung total berkas yang berstatus wajib dari template database
     */
    public function getRequiredDocumentCount(): int
    {
        return DocumentTemplate::where('is_shown', true)->where('is_required', true)->count();
    }

    public function completedReviewsCount(): int
    {
        return $this->reviews()->whereNotNull('submitted_at')->count();
    }

    public function totalAssignments(): int
    {
        return $this->assignments()->count();
    }
    
    public function ecCertificate()
    {
        return $this->hasOne(SubmissionDocument::class)
                    ->where('doc_type', 'EC_CERTIFICATE')
                    ->latestOfMany();
    }

    public function isEcPublished(): bool
    {
        return $this->status === SubmissionStatus::PUBLISHED;
    }
}