<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\EmployeeQuizAnswers;
use App\EmployeeQuiz;
use App\Quiz;
use App\QuizChoice;
use App\QuizPassingRate;
use App\QuizCertificate;
use App\Course;

use \FPDM as FPDM;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = EmployeeQuiz::select('emp_id','quiz_type','course_id')
            ->where('quiz_type','post')
            ->groupBy('emp_id','quiz_type','course_id')
            ->orderBy('created_at','desc')
            ->get();

        return view('admin.results.index', compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Quiz::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function setChoices(Request $request) {
        
        return QuizChoice::create($request->all());
    }

    public function setPassingRate(Request $request) {
        
        return QuizPassingRate::create($request->all());
    }

    //Employee Quizzes
    public function submitQuiz(Request $request) {

        $date_now = Carbon::now();
        $ids = json_decode($request->questions);
        $attempt = EmployeeQuiz::where([
                ['emp_id',Auth::user()->emp_id],
                ['course_id',$request->course_id],
                ['quiz_type',$request->quiz_type]
            ])->count();
        $passing_score = QuizPassingRate::where([['course_id',$request->course_id],['exam_type',$request->quiz_type],['attempt',($attempt+1)]])->first();
 
        $final_score = $score = 0;

        $quiz = EmployeeQuiz::create([
            'emp_id' => Auth::user()->emp_id,
            'course_id' => $request->course_id,
            'start' => $request->time_start,
            'end' => $date_now->toDateTimeString(),
            'quiz_type' => $request->quiz_type,
            'score' => $score
        ]);

        foreach($ids as $id) {
            if($request->has('q'.$id)) {
                
                $choice_id = $request->input('q'.$id);
                if(QuizChoice::find($choice_id)->is_correct)
                    $score++;

                EmployeeQuizAnswers::create([
                    'quiz_attempt_id' => $quiz->id,
                    'quiz_id' => $id,
                    'choice_id' => $choice_id
                ]);
            }
        }

        $final_score = ($score/count($ids)) * 100;

        EmployeeQuiz::where('id',$quiz->id)->update([
            'score' => $final_score
        ]);
        
        if($final_score>=$passing_score->score) {
            
            $count_cert = QuizCertificate::whereBetween('created_at',[$date_now->startOfYear()->toDateString(),$date_now->endOfYear()->toDateString()])->count()+1;
            QuizCertificate::create([
                'control_num' => 'PTU'.$date_now->year.'-'.Course::find($request->course_id)->code.'-'.str_pad($count_cert, 3, "0", STR_PAD_LEFT),
                'employee_quiz_id' => $quiz->id
            ]);
        }
        
        return $final_score;
    }

    public function getCertificate($id) {
                
        $quiz = QuizCertificate::find($id);
        if($quiz->EmployeeQuiz->verified_by && $quiz->EmployeeQuiz->verified_at) {
            $cert_date = Carbon::parse($quiz->created_at);
            $cert = 'template/BLS_cert_compiled.pdf';
            $fields = array(
                'control_num' => 'Control No.: '.$quiz->control_num,
                'name' => $quiz->EmployeeQuiz->employee->firstname.' '.$quiz->EmployeeQuiz->employee->lastname,
                'position' => $quiz->EmployeeQuiz->employee->position->position_title,
                'body' => 'for participating in the Basic Life Support Training held on the '.$cert_date->format('jS').' day of '.$cert_date->format('F Y').' at the Mariano Marcos Memorial Hospital and Medical Center Online Learning Management System.'
            );
            $pdf = new FPDM(public_path($cert));
            $pdf->Load($fields, true);
            $pdf->Merge();
            $pdf->Output();
        } else
        return '<a href="'.url('/').'">PETRO is verifying your score, please wait for the verification process. Thank you</a>';
    }

    public static function checkIfPassed($emp_id, $id) {
        
        $attempts = EmployeeQuiz::where([
            ['emp_id',Auth::user()->emp_id], ['course_id',$id], ['quiz_type','post']
        ])->orderBy('created_at')->get();
            
        $url = url('/course/get/certificate');
        $passingRates = QuizPassingRate::select('score')->where([['course_id',$id],['exam_type','post']])->orderBy('attempt')->get()->toArray();
        foreach($attempts as $k => $attempt) {
            
            if($attempt->certificate)
                $url = $url.'/'.$attempt->certificate->id;

            if($attempt->score >= $passingRates[$k]['score'])
                
                return array(
                    'verified' => ($attempt->verified_at!=null && $attempt->verified_by!=null) ? true : false,
                    'passed' => true,
                    'attempt_id' => $attempt->id,
                    'attempts' => count($attempts),
                    'score' => $attempt->score,
                    'passing_score' => $passingRates[$k]['score'],
                    'certificate_url' =>  $url
                );
        }

        return array(
            'passed' => false,
            'attempts' => count($attempts)
        );
    }

    public function verify($course_id, $emp_id) {
        
        EmployeeQuiz::where([['emp_id',$emp_id], ['course_id',$course_id], ['quiz_type','post']])->update([
            'verified_by' => Auth::user()->emp_id,
            'verified_at' => Carbon::now()->toDateString()
        ]);

        return redirect()->back()->with('message','Post Test Result has been verified!');
    }
}
