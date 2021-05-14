<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    //
    protected $fillable = ['course_id', 'module_name', 'module_slug', 'module_type','video_url', 'module_content', 'module_image'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function employeeCourse() {
        return $this->belongsTo(EmployeeCourse::class,'id','module_id');
    }
}
