<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExamAnswer extends Model
{
    protected $table = 'user_exam_answers';

    protected $fillable = [
        'user_exam_attempt_id',
        'question_id',
        'selected_answer_id',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(UserExamAttempt::class, 'user_exam_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function selectedAnswer()
    {
        return $this->belongsTo(Answer::class, 'selected_answer_id');
    }
}
