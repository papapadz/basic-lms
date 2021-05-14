<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeQuiz extends Model
{

    protected $fillable = [
        'emp_id', 'course_id', 'start', 'end', 'quiz_type', 'score'
    ];
}
