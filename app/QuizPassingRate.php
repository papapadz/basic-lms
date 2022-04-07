<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizPassingRate extends Model
{
    public $fillable = [
        'course_id', 'attempt', 'score', 'exam_type'
    ];
}
