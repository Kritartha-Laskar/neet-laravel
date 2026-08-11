<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = ['subject_id', 'study_class_id', 'question', 'image', 'question_type', 'sort_order'];

    protected $with = ['answers'];

    // ── Accessors ────────────────────────────────────────────────

    /** Full public URL to the image (if any) */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? Storage::url($this->image)
            : null;
    }

    // ── Relationships ────────────────────────────────────────────

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function studyClass()
    {
        return $this->belongsTo(StudyClass::class, 'study_class_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
