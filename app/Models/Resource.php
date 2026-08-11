<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'study_class_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'thumbnail_path',
        'subject',
        'sort_order',
        'is_active',
    ];

    public function studyClass()
    {
        return $this->belongsTo(StudyClass::class, 'study_class_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Accessors ────────────────────────────────────────────────

    /** Full public URL to the file */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /** Full public URL to the thumbnail (if any) */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path
            ? Storage::url($this->thumbnail_path)
            : null;
    }

    /** Human-readable file size */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1_073_741_824) return round($bytes / 1_073_741_824, 2) . ' GB';
        if ($bytes >= 1_048_576)     return round($bytes / 1_048_576, 2)     . ' MB';
        if ($bytes >= 1_024)         return round($bytes / 1_024, 2)         . ' KB';
        return $bytes . ' B';
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeVideos($query)  { return $query->where('type', 'video'); }
    public function scopePdfs($query)    { return $query->where('type', 'pdf'); }
    public function scopeImages($query)  { return $query->where('type', 'image'); }
    public function scopeActive($query)  { return $query->where('is_active', true); }
}
