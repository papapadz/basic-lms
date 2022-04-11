<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeCourse extends Model
{
    protected $fillable = [
        'emp_id','course_id','module_id','finished_date'
    ];

    public function module() {
        return $this->hasOne(Module::class,'id','module_id');
    }

    public function employee() {
        return $this->belongsTo(Employee::class,'emp_id','emp_id');
    }

    public function course() {
        return $this->belongsTo(Course::class,'course_id','id');
    }

    public function quiz() {
        return $this->hasMany(EmployeeQuiz::class,'emp_id','emp_id');
    }
}
