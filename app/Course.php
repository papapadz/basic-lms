<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable = ['course_name', 'course_slug', 'course_description','course_image'];


    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function quizzes() {
        return $this->hasMany(Quiz::class,'course_id','id')->with('choices');
    }
}
