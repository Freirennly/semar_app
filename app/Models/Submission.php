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
        'abstract', 'submitted_at', 'decided_at',
    ];

    protected $casts = [
        'status' => SubmissionStatus::class,
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

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

    public function hasAllDocuments(): bool
    {
        $required = collect(DocType::cases())->pluck('value');
        
        // Tambahkan fungsi map() untuk mengurai objek Enum menjadi teks murni
        $uploaded = $this->documents()->pluck('doc_type')->map(function ($enum) {
            return $enum->value ?? $enum;
        });

        return $required->diff($uploaded)->isEmpty();
    }

    public function getDocumentCount(): int
    {
        return $this->documents()->count();
    }

    public function getRequiredDocumentCount(): int
    {
        return count(DocType::cases());
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
