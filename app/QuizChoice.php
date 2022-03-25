<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizChoice extends Model
{
    public $fillable = [
        'quiz_id', 'choice', 'is_correct'
    ];
}
