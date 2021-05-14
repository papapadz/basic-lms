<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    public function course() {
        return $this->belongsTo(Course::class,'id','course_id');
    }

    public function choices() {
        return $this->hasMany(QuizChoice::class,'quiz_id','id');
    }
}
