<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subject_id',
        'description',
        'sort_order',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get resources belonging to this class, ordered by sort_order serial
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'study_class_id')->orderBy('sort_order');
    }

    /**
     * Get questions belonging to this class, ordered by sort_order serial
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'study_class_id')->orderBy('sort_order');
    }
}
