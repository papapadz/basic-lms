<?php

namespace App\Http\Controllers;
use App\Course;
use App\Module;
use Illuminate\Http\Request;
Use Image;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\EmployeeCourse;
use App\Quiz;
use App\EmployeeQuiz;

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
        //
        $courses = Course::all();
        return view('admin.courses.index')->with('courses',$courses);
    }

    public function homepage()
    {
        //
        $courses = Course::all();
        return view('dashboard')->with('courses',$courses);
    }


    public function course($course)
    {
        //loads a specific course by direct url
        $course = Course::where('course_slug', '=', $course)->firstOrFail();
        $modules = Module::where("course_id", "=", $course->id)->get();
        $slug = $modules[0]->module_slug;

        $isEnrolled = false;
        $empCourse = EmployeeCourse::where([['emp_id',Auth::user()->emp_id],['course_id',$course->id]])->first();
        if($empCourse)
            $slug = Module::find($empCourse->module_id)->module_slug;

        $url = url('/course').'/'.$course->course_slug.'/'.$slug;
        return view('courses.tutorial', compact('course','empCourse','url'))->with('modules', $modules);
    }

    public function module($course, $module)
    {    
        //loads a specific lesson's module by direct url
        $course = Course::where('course_slug', '=', $course)->firstOrFail();
        $module = Module::where('module_slug', '=', $module)->firstOrFail();
        $modules = Module::where([
                ["course_id", "=", $course->id],['module_order',$module->module_order+1]
            ])->get();
        $questions = [];
        $passing = [75,80,85];
        $attempts = [];

        //tracks the progress
        $empCourse = EmployeeCourse::where([['emp_id',Auth::user()->emp_id],['course_id',$course->id]])->first();
        if($empCourse)
            $empCourse->update(['module_id'=>$module->id]);
        else
            EmployeeCourse::create([
                'emp_id' => Auth::user()->emp_id, 'course_id' => $course->id, 'module_id' => $module->id
            ]);
        
        //prepare random quiz questions
        if($module->module_type=='pre' || $module->module_type=='post') {
            
            $attempts = EmployeeQuiz::where([
                ['emp_id',Auth::user()->emp_id], ['course_id',$course->id], ['quiz_type',$module->module_type]
            ])->orderBy('created_at')->get();
            
            if(count($attempts)<3) {
                if($module->module_type=='pre')
                    $num_q = 5;
                else if($module->module_type=='post')
                    $num_q = 15;
                $random = $course->quizzes->where('quiz_type', $module->module_type)->pluck('id')->toArray();
                $arr_q = array_rand($random,$num_q);

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
        return view('courses.index', compact('course','questions','attempts','passing'))->with('module', $module)->with('modules', $modules);
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
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'course_name' => 'required|max:255',
            'course_slug' => 'required|unique:courses|max:50',
            'course_description' => 'max: 800',
            'course_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $course = new Course;
        $course->course_name = $request->course_name;
        $course->course_slug = $request->course_slug;
        $course->course_description = $request->course_description;
        if($request->hasFile('course_image')) {
            $filename = $fileName = time().'.'.$request->course_image->extension();
            $request->course_image->move(public_path('images/courses'), $fileName);
            $course->course_image = $filename;
            // $image  = $request->file('course_image')->store('courses');
            // $course->course_image = Storage::url($image);          
        }
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
        $modules = Module::where("course_id", "=", $course->id)->get();
        return view('courses.summary', compact('course'))->with('modules', $modules)->with('course', $course);
    }

    public function done($course_id) {

    }

}
