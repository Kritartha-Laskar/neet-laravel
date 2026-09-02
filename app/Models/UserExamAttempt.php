<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExamAttempt extends Model
{
    protected $table = 'user_exam_attempts';

    protected $fillable = [
        'user_id',
        'question_paper_id',
        'status',
        'total_questions',
        'total_answered',
        'correct_answers',
        'wrong_answers',
        'total_marks',
        'score',
        'percentage',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'percentage' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questionPaper()
    {
        return $this->belongsTo(QuestionPaper::class, 'question_paper_id');
    }

    public function answers()
    {
        return $this->hasMany(UserExamAnswer::class, 'user_exam_attempt_id');
    }
}
