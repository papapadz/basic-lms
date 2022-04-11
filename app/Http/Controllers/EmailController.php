<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuizCertificate;
use Carbon\Carbon;

class EmailController extends Controller
{
    public function  __construct() {
        $this->middleware('auth');
    }

    public function send($quizCertificate) {
        
        $data = [
            'email' => $quizCertificate->EmployeeQuiz->employee->email,
            'title' => $quizCertificate->EmployeeQuiz->course->course_name,
            'subject' => 'Certificate on '.$quizCertificate->EmployeeQuiz->course->course_name,
            'certificate_url' => url('course/get/certificate/'.$quizCertificate->id),
            'date' => Carbon::parse($quizCertificate->EmployeeQuiz->start)->toFormattedDateString()
        ];

        dispatch(new \App\Jobs\SendEmailJob($data));
    }
}
