<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\EmployeeQuizAnswers;
use App\EmployeeQuiz;
use App\QuizChoice;
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
        //
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

    //Employee Quizzes
    public function submitQuiz(Request $request) {
        
        $ids = json_decode($request->questions);
        $attempt = EmployeeQuiz::where([['emp_id',Auth::user()->emp_id],['course_id',$request->course_id]])->count();
        $score = 0;

        $quiz = EmployeeQuiz::create([
            'emp_id' => Auth::user()->emp_id,
            'course_id' => $request->course_id,
            'start' => $request->time_start,
            'end' => Carbon::now()->toDateTimeString(),
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

        EmployeeQuiz::where('id',$quiz->id)->update([
            'score' => ($score/count($ids)) * 100
        ]);

        return $score;
    }

    public function getCertificate($id) {

        $attempts = EmployeeQuiz::where([
            ['emp_id',Auth::user()->emp_id], ['course_id',$id], ['quiz_type','post']
        ])->orderBy('created_at')->get();

        $passing = [75,80,85];
        $index = (count($attempts)-1);
        $cert_date = Carbon::parse($attempts[$index]->created_at);
        if($attempts[$index]->score >= $passing[$index]) {
            $cert = 'template/bls_cert.pdf';
            $fields = array(
                'name' => Auth::user()->employee->firstname.' '.Auth::user()->employee->lastname,
                'body' => 'who has successfully completed the Online Basic Life Support Training module conducted by Mariano Marcos Memorial Hospital and Medical Center. Completed this '.$cert_date->format('jS').' day of '.$cert_date->format('F Y')
            );
            $pdf = new FPDM(public_path($cert));
            $pdf->Load($fields, true);
            $pdf->Merge();
            $pdf->Output();
        } else
        return '<a href="/">You did not pass the Post Test. Please coordinate with PETU to schedule a face to face training. Thank you</a>';
    }
}
