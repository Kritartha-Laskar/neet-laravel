<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $table = 'chapters';

    protected $fillable = [
        'subject_id',
        'course_id',
        'chapter_number',
        'name',
        'description',
        'sort_order',
    ];

    /**
     * Subject relationship.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Course relationship.
     */
    public function course()
    {
        return $this->belongsTo(CourseName::class, 'course_id');
    }

    /**
     * Questions belonging to this chapter.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'chapter_id');
    }

    /**
     * Question Papers belonging to this chapter.
     */
    public function questionPapers()
    {
        return $this->hasMany(QuestionPaper::class, 'chapter_id');
    }

    /**
     * Display Title helper (e.g. "Chapter 1: Cell Structure" or "Cell Structure").
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->chapter_number) {
            $numPrefix = is_numeric($this->chapter_number) ? ("Chapter " . $this->chapter_number) : $this->chapter_number;
            return $numPrefix . ': ' . $this->name;
        }
        return $this->name;
    }
}
