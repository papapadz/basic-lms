<?php

namespace App\Http\Controllers;
use App\Course;
use App\Module;
use Illuminate\Http\Request;
Use Image;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;
use App\EmployeeCourse;
use App\Quiz;
use App\EmployeeQuiz;
use App\QuizPassingRate;
use App\QuizCertificate;
use App\Employee;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function admin(){
        return view('admin.index');
     }
    public function index()
    {
        if(Auth::User()->role==1)
            $courses = Course::all();
        else {
            $courseAssigned = Auth::User()->courseReviewer;
            $courseArr = [];
            foreach($courseAssigned as $course)
                array_push($courseArr, $course->course_id);
            $courses = Course::whereIn('id',$courseArr)->get();
        }
        return view('admin.courses.index')->with('courses',$courses);
    }

    public function homepage()
    {
        //
        $courses = Course::all();
        return view('dashboard')->with('courses',$courses);
    }


    public function course($course_slug)
    {
        //loads a specific course by direct url
        $course = Course::where('course_slug', '=', $course_slug)->firstOrFail();
        
        if((count($course->modules)>=1 && $course->is_active) || Auth::User()->role == 1 || Auth::User()->courseReviewer->where('course_id',$course->id)->first()) {
            $modules = Module::where("course_id", "=", $course->id)->orderBy('module_order')->get();
            $slug = $modules[0]->module_slug;
            
            $isEnrolled = false;
            $empCourse = EmployeeCourse::where([['emp_id',Auth::User()->emp_id],['course_id',$course->id]])->first();
            if($empCourse)
                $slug = Module::find($empCourse->module_id)->module_slug;

            $url = url('/course').'/'.$course->course_slug.'/'.$slug;
            
            $attempts = QuizController::checkIfPassed(Auth::User()->emp_id,$course->id);
            
            return view('courses.tutorial', compact('course','empCourse','url','attempts'))->with('modules', $modules);
        } else if($course->enrollees->where('emp_id',Auth::User()->emp_id)->first()) {
            return redirect()->route('summary',[$course_slug]);
        } else
            return redirect()->back()->with('danger-message','Course is not yet available!');
        
    }

    public function module($course, $module)
    {    
        //loads a specific lesson's module by direct url
        $course = Course::where('course_slug', '=', $course)->firstOrFail();
        $module = Module::where([['course_id',$course->id],['module_slug', '=', $module]])->firstOrFail();
        $modules = Module::where([
                ["course_id", "=", $course->id],['module_order',$module->module_order+1]
            ])->orderBy('module_order')->get();
        $questions = [];
        $passing = QuizPassingRate::where([['course_id',$course->id],['exam_type',$module->module_type]])->orderBy('attempt')->get();
        
        $attempts = [];
        $passed = false;

        //tracks the progress
        // $empCourse = EmployeeCourse::where([['emp_id',Auth::user()->emp_id],['course_id',$course->id]])->first();
        // if($empCourse && $empCourse->finished_date==null)
        //     $empCourse->update(['module_id'=>$module->id]);
        // else
        //     EmployeeCourse::firstOrcreate([
        //         'emp_id' => Auth::user()->emp_id, 'course_id' => $course->id, 'module_id' => $module->id
        //     ]);
        Employee::updateOrCreate([
            'emp_id' => Auth::user()->emp_id, 'course_id' => $course->id,
        ],[
            'module_id' => $module->id
        ]);
        
        //prepare random quiz questions
        if($module->module_type=='pre' || $module->module_type=='post') {
            
            $attempts = EmployeeQuiz::where([
                ['emp_id',Auth::user()->emp_id], ['course_id',$course->id], ['quiz_type',$module->module_type]
            ])->orderBy('created_at')->get();
            
            if(count($attempts)<count($passing)) {

                if(count($attempts)>0) {
                    foreach($attempts as $k => $attempt)
                        if($attempt->score>=$passing[$k]->score) {
                            $passed = true;
                            break;
                        }
                }
                if(!$passed) {
                    $random = $course->quizzes->where('quiz_type', $module->module_type)->pluck('id')->toArray();
                   
                    if($module->module_type=='pre')
                        if(count($random)<5) {
                            $arr_q = $random;
                        } else
                            $arr_q = array_rand($random,5);
                    else if($module->module_type=='post')
                        if(count($random)<15) {
                            $arr_q = $random;
                        } else
                            $arr_q = array_rand($random,15);
                    
                    foreach($arr_q as $r) {
                        $q = Quiz::find($r);
                        
                        if($q)
                            array_push($questions,$q);
                        else {
                            if($module->module_type=='exam-pre')
                                $add_q = $course->quizzes->whereNotIn('id', $arr_q)->where('quiz_type','pre')->first();
                            else
                                $add_q = $course->quizzes->whereNotIn('id', $arr_q)->where('quiz_type','post')->first();
                            array_push($questions,$add_q);
                        }
                    }
                }
            }  

            // if(count($attempts)>=1) {
            //     $index = (count($attempts)-1);
                    
            //     if($attempts[$index]->score >= $passing[$index]->score)
            //             $passed = true;
            // }
        }
        
        return view('courses.index', compact('course','questions','attempts','passing','passed'))->with('module', $module)->with('modules', $modules);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'code' => 'required|max:5',
            'course_name' => 'required|max:255',
            'course_slug' => 'required|unique:courses|max:50',
            'course_description' => 'required|max: 800',
            'content' => 'required|max: 500',
            'course_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'course_certificate' => 'required|mimes:pdf'
        ]);
        $course = new Course;
        $course->course_name = $request->course_name;
        $course->course_slug = $request->course_slug;
        $course->code = $request->code;
        $course->course_description = $request->course_description;
        $course->content = $request->content;
        $course->post_notes = $request->postnote;
        if($request->hasFile('course_image')) {
            $filename = $fileName = time().'.'.$request->course_image->extension();
            $request->course_image->move(public_path('images/courses'), $fileName);
            $course->course_image = $filename;
            // $image  = $request->file('course_image')->store('courses');
            // $course->course_image = Storage::url($image);          
        }
        if($request->hasFile('course_certificate')) {
            $filename = $fileName = $request->code.'.'.$request->course_certificate->extension();
            $request->course_certificate->move(public_path('template'), $fileName);
            $course->course_cert = $filename;         
        }
        $course->needs_verification = $request->needs_verification;
        $course->save();
        return redirect()->route('admin.courses.index')->with('message', 'Course successfully updated!');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        //
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        //
        $request->validate([
            'course_name' => 'max:255',
            'course_slug' => 'max:50',
            'course_description' => 'max: 800',
            'course_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        
        $course->update($request->all());
        $course->course_name = $request->course_name;
        $course->course_slug = $request->course_slug;
        $course->course_description = $request->course_description;
        if($request->hasFile('course_image')) {
            $filename = $fileName = time().'.'.$request->course_image->extension();
            $request->course_image->move(public_path('images/courses'), $fileName);
            $course->course_image = $filename;
            // $image       = $request->file('course_image')->store('courses');
            // $course->course_image = Storage::url($image);
        }
        $course->save();
        return redirect()->route('admin.courses.index')->with('message', 'Course successfully updated!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        //
        $course->delete();
        return redirect()->route('admin.courses.index')->with('danger-message', 'Course successfully deleted!');
    }

    public function summary($course)
    {
        //loads a specific course's lessons by direct url - this is the lesson summary
        $course = Course::where('course_slug', '=', $course)->firstOrFail();
        $modules = Module::where("course_id", "=", $course->id)->orderBy('module_order')->get();
        $attempts = EmployeeQuiz::where([
            ['emp_id',Auth::user()->emp_id], ['course_id',$course->id], ['quiz_type','post']
        ])->orderBy('created_at')->get();
        $passing = QuizPassingRate::where('course_id',$course->id)->orderBy('attempt')->get();
        
        $empCourse = EmployeeCourse::where([['course_id',$course->id],['emp_id',Auth::user()->emp_id]])->first();
        
        if(!$empCourse->finished_date)
            EmployeeCourse::where([['course_id',$course->id],['emp_id',Auth::user()->emp_id]])->update(['finished_date'=>Carbon::now()->toDateTimeString()]);

        return view('courses.summary', compact('course'))->with([
            'modules' => $modules,
            'course' => $course,
            'attempts' => $attempts,
            'passing' => $passing,
            //'empCourse' => $empCourse
        ]);

    }

    public function setActive(Request $request) {
        $course = Course::find($request->id);
        
        if($course->is_active)
            Course::where('id',$request->id)->update(['is_active'=>false]);
        else
            Course::where('id',$request->id)->update(['is_active'=>true]);
    }

    public function enrollees($course_id) {
        return view('admin.enrollees.index')->with('course',Course::find($course_id));
    }

}
