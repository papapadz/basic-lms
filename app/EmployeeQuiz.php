<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeQuiz extends Model
{

    protected $fillable = [
        'emp_id', 'course_id', 'start', 'end', 'quiz_type', 'score'
    ];

    public function certificate() {
        return $this->hasOne(QuizCertificate::class,'employee_quiz_id','id');
    }

    public function employee() {
        return $this->hasOne(Employee::class,'emp_id','emp_id')->with('position');
    }
}
