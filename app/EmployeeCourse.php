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
}
