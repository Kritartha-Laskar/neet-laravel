<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['course_id', 'name'];

    public function course()
    {
        return $this->belongsTo(CourseName::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
