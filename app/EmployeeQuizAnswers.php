<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeQuizAnswers extends Model
{
    protected $fillable = [
        'quiz_attempt_id', 'quiz_id', 'choice_id'
    ];

    public function quiz() {
        return $this->belongsTo(Quiz::class,'quiz_id','id');
    }
}
