<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeCourse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'emp_id','course_id','module_id','finished_date','deleted_at','file_id','remarks'
    ];

    public function module() {
        return $this->hasOne(Module::class,'id','module_id');
    }

    public function employee() {
        return $this->belongsTo(Employee::class,'emp_id','emp_id')->with('department');
    }

    public function course() {
        return $this->belongsTo(Course::class,'course_id','id')->with('modules');
    }

    public function quiz() {
        return $this->hasMany(EmployeeQuiz::class,'emp_course_id','id')->orderBy('employee_quizzes.created_at')->with('certificate');
    }

    public function file() {
        return $this->hasOne(FileHandler::class,'id','file_id');
    }

    // public function department() {
    //     return $this->hasOneThrough(
    //         Department::class,
    //         Employee::class,
    //         'emp_id',
    //         'department_id',
    //         'emp_id',
    //         'department_id'
    //     );
    // }
}
