<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    protected $fillable = [
        'user_id', 'course_id'
    ];

    public function user() {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function course() {
        return $this->hasOne(Course::class,'id','course_id');
    }
}
