<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = ['subject_id', 'study_class_id', 'question', 'question_type', 'sort_order'];

    protected $with = ['answers'];

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
