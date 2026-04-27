<?php

namespace App\Models;

use App\Enums\DocType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionDocument extends Model
{
    protected $fillable = [
        'submission_id', 'doc_type', 'file_path',
        'original_name', 'mime', 'size', 'uploaded_by',
    ];

    protected $casts = [
        'doc_type' => DocType::class,
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
