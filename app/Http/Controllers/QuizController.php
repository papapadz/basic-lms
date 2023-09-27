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
use App\EmployeeCourse;

use \FPDM as FPDM;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::User()->role==1)
            $results = EmployeeQuiz::select('emp_id','quiz_type','course_id')
                ->where('quiz_type','post')
                ->groupBy('emp_id','quiz_type','course_id')
                ->orderBy('created_at','desc')
                ->get();
        else {
            $courseAssigned = Auth::User()->courseReviewer;
            $courseArr = [];
            foreach($courseAssigned as $course)
                array_push($courseArr, $course->course_id);
        
            $results = EmployeeQuiz::select('emp_id','quiz_type','course_id')
                ->where('quiz_type','post')
                ->groupBy('emp_id','quiz_type','course_id')
                ->whereIn('course_id',$courseArr)
                ->orderBy('created_at','desc')
                ->get();
        }
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
                ['emp_course_id',$request->emp_course_id],
                ['quiz_type',$request->quiz_type]
            ])->count();

        $passing_score = QuizPassingRate::where([['course_id',$request->course_id],['exam_type',$request->quiz_type],['attempt',($attempt+1)]])->first();
 
        $final_score = $score = 0;

        $quiz = EmployeeQuiz::create([
            'emp_id' => Auth::user()->emp_id,
            'emp_course_id' => $request->emp_course_id,
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
        
        if($final_score>=$passing_score->score && $request->quiz_type=='post') {
            
            EmployeeCourse::where([
                ['course_id', $request->course_id],
                ['emp_id',Auth::user()->emp_id],
                ['finished_date',null]
            ])->update(['finished_date' => Carbon::now()->toDateString()]);

            $count_cert = QuizCertificate::whereBetween('created_at',[$date_now->startOfYear()->toDateString(),$date_now->endOfYear()->toDateString()])->count()+1;
            $certificate = QuizCertificate::create([
                'control_num' => 'PTRO-'.$date_now->year.'-'.Course::find($request->course_id)->code.'-'.str_pad($count_cert, 3, "0", STR_PAD_LEFT),
                'employee_quiz_id' => $quiz->id
            ]);

            $course = Course::find($request->course_id);
            if(!$course->needs_verification) {
                EmployeeQuiz::where('id',$quiz->id)->update([
                    'verified_by' => Auth::user()->id,
                    'verified_at' => Carbon::now()->toDateString()
                ]);
                $emailController = new EmailController;
                $emailController->send($certificate);
            }
            
        }
        
        return $final_score;
    }

    public function getCertificate($id) {
                
        $quiz = QuizCertificate::find($id);       
        //if($quiz->EmployeeQuiz->verified_by && $quiz->EmployeeQuiz->verified_at) {
        if($quiz) {
            $cert_date = Carbon::parse($quiz->created_at);
            $cert = $quiz->EmployeeQuiz->course->course->course_cert;
            $mi = $quiz->EmployeeQuiz->employee->middlename ? ' '.$quiz->EmployeeQuiz->employee->middlename[0].'. ' : '';
            $fields = array(
                //'control_num' => 'Control No.: '.$quiz->control_num,
                'name' => $quiz->EmployeeQuiz->employee->firstname.$mi.$quiz->EmployeeQuiz->employee->lastname,
                //'position' => $quiz->EmployeeQuiz->employee->position->position_title,
                //'body' => 'for participating in the Basic Life Support Training held on the '.$cert_date->format('jS').' day of '.$cert_date->format('F Y').' at the Mariano Marcos Memorial Hospital and Medical Center Online Learning Management System.'
            );
            if($quiz->EmployeeQuiz->course->course->id == 1)
                $fields = array_merge($fields, [
                    'control_num' => 'Control No.: '.$quiz->control_num,
                    'position' => $quiz->EmployeeQuiz->employee->position->position_title,
                    'body' => 'for participating in the Basic Life Support Training held on the '.$cert_date->format('jS').' day of '.$cert_date->format('F Y').' at the Mariano Marcos Memorial Hospital and Medical Center Online Learning Management System.'
                ]);
            $pdf = new FPDM(public_path($cert));
            $pdf->Load($fields, true);
            $pdf->Merge();
            $pdf->Output();
        } else return redirect()->back()->with('error','No certificate Found!');
    }

    public static function checkIfPassed($emp_id, $emp_course_id) {

        $attempts = EmployeeQuiz::where([['emp_course_id',$emp_course_id],['quiz_type','post']])->orderBy('created_at')->get();
        
        $url = url('/course/get/certificate');
        $passingRates = QuizPassingRate::select('score')->where([['course_id',EmployeeCourse::find($emp_course_id)->course->id],['exam_type','post']])->orderBy('attempt')->get()->toArray();
        
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
            
            if($k+1==count($passingRates))
                break;
        }

        return array(
            'passed' => false,
            'attempts' => count($attempts)
        );
    }

    public function verifyUsingQuizID($id) {
        $empQuiz = EmployeeQuiz::find($id);
        $empQuiz->update([
            'verified_by' => Auth::user()->id,
            'verified_at' => Carbon::now()->toDateTimeString()
        ]);
        $empCourse = EmployeeCourse::find($empQuiz->emp_course_id);
        if(!$empCourse->finished_date)
            $empCourse->update(['finished_date' => Carbon::now()->toDateTimeString()]);
        $emailController = new EmailController;
        $emailController->send($empQuiz->certificate);
        return redirect()->back()->with('message','Post Test Result has been verified!');
    }

    // public function verify($course_id, $emp_id) {
        
    //     // $quiz = EmployeeQuiz::where([['emp_id',$emp_id], ['course_id',$course_id], ['quiz_type','post']])->update([
    //     //     'verified_by' => Auth::user()->id,
    //     //     'verified_at' => Carbon::now()->toDateString()
    //     // ]);
    //     $quiz = EmployeeQuiz::where([['emp_id',$emp_id], ['course_id',$course_id], ['quiz_type','post']])->get()->pluck('id');
        // $certificate = QuizCertificate::whereIn('employee_quiz_id',$quiz->toArray())->first();
        // EmployeeQuiz::where('id',$certificate->employee_quiz_id)->update([
        //         'verified_by' => Auth::user()->id,
        //         'verified_at' => Carbon::now()->toDateString()
        // ]);
        // $emailController = new EmailController;
        // $emailController->send($certificate);

    //     return redirect()->back()->with('message','Post Test Result has been verified!');
    // }

    public function createCertificate(Request $request) {
        Validator::make($request->all(), [
            'emp_id' => 'required',
            'course_id' => 'required',
            'score' => 'required|numeric|min:1|max:100'
        ])->validate();
        
        $date_now = Carbon::now();
        $emp_id = str_pad($request->emp_id, 6, "0", STR_PAD_LEFT);

        $empCourse = EmployeeCourse::find($request->course_id)->first();

        $quiz = EmployeeQuiz::create([
            'emp_id' => $emp_id,
            'emp_course_id' => $request->course_id,
            'start' => $empCourse->created_at,
            'end' => $empCourse->finished_date,
            'quiz_type' => 'post',
            'score' => $request->score,
            'verified_by' => Auth::user()->id,
            'verified_at' => $date_now->toDateTimeString()
        ]);
        
        $count_cert = QuizCertificate::whereBetween('created_at',[$date_now->startOfYear()->toDateString(),$date_now->endOfYear()->toDateString()])->count()+266;
        $quizCertificate = QuizCertificate::create([
                'control_num' => 'PTRO-'.$date_now->year.'-'.str_pad($count_cert, 3, "0", STR_PAD_LEFT),
                'employee_quiz_id' => $quiz->id
            ]);

        $emailController = new EmailController;
        $emailController->send($quizCertificate);
    }
}
