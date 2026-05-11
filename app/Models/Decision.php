<?php

namespace App\Models;

use App\Enums\DecisionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Decision extends Model
{
    protected $fillable = [
        'submission_id', 'decided_by', 'decision', 'notes', 'decided_at',
    ];

    protected $casts = [
        'decision' => DecisionType::class,
        'decided_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
