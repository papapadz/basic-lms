<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizCertificate extends Model
{
    protected $fillable = [
        'control_num', 'employee_quiz_id'
    ];

    public function EmployeeQuiz() {
        return $this->belongsTo(EmployeeQuiz::class,'employee_quiz_id','id');
    }
}
