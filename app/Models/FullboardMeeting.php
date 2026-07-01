<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullboardMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'meeting_type',
        'scheduled_at',
        'end_at',
        'location',
        'meeting_url',
        'agenda',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
