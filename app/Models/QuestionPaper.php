<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionPaper extends Model
{
    protected $table = 'question_paper';

    protected $fillable = [
        'title',
        'description',
        'exam_name',
        'course_id',
        'paper_type',
        'subject_id',
        'subject_quotas',
        'total_questions',
        'duration_minutes',
        'total_marks',
        'exam_year',
    ];

    protected $casts = [
        'subject_quotas' => 'array',
    ];

    /**
     * Course relationship for Question Paper.
     */
    public function course()
    {
        return $this->belongsTo(CourseName::class, 'course_id');
    }

    /**
     * Subject relationship for Subject-Wise Mock Test Papers.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Check if paper is a subject-wise mock test paper.
     */
    public function isMockTest(): bool
    {
        return $this->paper_type === 'mocktest';
    }

    /**
     * Check if paper is a combined multi-subject paper.
     */
    public function isCombined(): bool
    {
        return $this->paper_type === 'combined' || empty($this->paper_type);
    }

    /**
     * All questions belonging to this paper (via pivot).
     */
    public function questions()
    {
        return $this->belongsToMany(
            Question::class,
            'question_paper_question',   // pivot table
            'question_paper_id',
            'question_id'
        )->withPivot('order', 'marks')
         ->orderByPivot('order');
    }

    /**
     * Count of questions per subject in this paper.
     * Returns a Collection keyed by subject name.
     */
    public function questionsBySubject()
    {
        return $this->questions()
                    ->with('subject')
                    ->get()
                    ->groupBy(fn($q) => optional($q->subject)->name ?? 'Unassigned');
    }
}
