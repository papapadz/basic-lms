<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable = ['course_name', 'course_slug', 'code', 'course_description','post_notes','course_image','course_cert'];


    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function quizzes() {
        return $this->hasMany(Quiz::class,'course_id','id')->with('choices');
    }

    public function passingRates() {
        return $this->hasMany(QuizPassingRate::class,'course_id','id');
    }
    
    public function enrollees() {
        return $this->hasMany(EmployeeCourse::class,'course_id','id');
    }
}
