<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'description',
        'is_required',
        'is_shown'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_shown' => 'boolean',
    ];

    // Helper untuk mengambil ukuran file
    public function getFileSizeAttribute()
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            $bytes = Storage::disk('public')->size($this->file_path);
            if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
            if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
            return $bytes . ' bita';
        }
        return '-';
    }
}