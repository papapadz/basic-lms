<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeQuiz extends Model
{

    protected $fillable = [
        'emp_id', 'emp_course_id', 'start', 'end', 'quiz_type', 'score', 'verified_by', 'verified_at'
    ];

    public function course() {
        return $this->hasOne(EmployeeCourse::class,'id','emp_course_id')->withTrashed()->with('course');
    }

    public function certificate() {
        return $this->hasOne(QuizCertificate::class,'employee_quiz_id','id');
    }

    public function employee() {
        return $this->hasOne(Employee::class,'emp_id','emp_id')->with('position');
    }

    public function lists($course_id,$emp_id) {
        return EmployeeQuiz::where([['course_id',$course_id],['emp_id',$emp_id],['quiz_type','post']])->orderBy('created_at')->get();
    }
}
