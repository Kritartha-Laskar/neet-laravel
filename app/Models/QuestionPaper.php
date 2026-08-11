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
        'total_questions',
        'duration_minutes',
        'total_marks',
        'exam_year',
    ];

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
