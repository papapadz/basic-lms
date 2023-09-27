<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::middleware('auth')->group(function() {

// });


Auth::routes();
Route::get('/', function() {
    return redirect()->route('homepage');
});
Route::middleware('auth')->group(function() {
    Route::get('/home', 'CourseController@homepage')->name('homepage');

    Route::get('course/{course}', 'CourseController@course')->name('course');
    Route::get('course/{course}/{module}', 'CourseController@module')->name('module');
    Route::get('/{course}/summary', 'CourseController@summary')->name('summary'); 
    Route::get('user/{course}/summary/{id}', 'CourseController@userSummary')->name('user.summary'); 
    Route::get('/admin', 'CourseController@admin')->middleware('admin')->name('admin');

    Route::resource('quiz', 'QuizController');
    Route::post('quiz/submit', 'QuizController@submitQuiz')->name('quiz.submit');
    Route::get('course/done/{course_id}','CourseController@done');
    Route::get('course/get/certificate/{id}','QuizController@getCertificate')->name('get-certificate'); 

    /** USER */
    Route::resource('user','UserController');
    Route::post('course/{emp_course}/certificate/upload','CourseController@uploadCertificate')->name('certificate.upload');
});


Route::prefix('admin')->name('admin.')->middleware('auth','admin')->group(function (){
    Route::resource('courses', 'CourseController');
    Route::resource('modules', 'ModuleController');
    Route::post('course/set/active','CourseController@setActive');
    Route::get('results','QuizController@index');
    Route::post('generate/certificate','QuizController@createCertificate');
    Route::get('enrollees/{course_id}','CourseController@enrollees')->name('enrollees.index');
    Route::get('enrollees/info/{emp_course_id}','CourseController@viewEnrollee')->name('enrollees.info');
    //Route::post('results/verify/{course_id}/{emp_id}','QuizController@verify')->name('results.verify');
    Route::get('results/verify/quiz/{id}','QuizController@verifyUsingQuizID')->name('results.verifyUsingQuizID');

    Route::get('enrollees/course/delete/{emp_course_id}','CourseController@deleteEmployeeCourse')->name('enrollees.delete.employee_course');

    Route::get('dashboard/data/get','AdminController@getDashboardData')->name('get_dashboard');

    Route::get('cleanup', function() {
        echo 'Cleaning started... <br>';
        $empCourses = App\EmployeeCourse::where('course_id',1)->get();
        foreach($empCourses as $i => $empCourse) {
            echo '#'.$i.'----------------------------------------------------------------';
            echo 'Cleaning record '.$empCourse->id.'...<br>';
            $postTests = $empCourse->quiz->where('quiz_type','post');
            $passed = false;
            $dateFinished = null;
            foreach($postTests as $k => $test) {
                echo 'Checking post test #'.$k.'...<br>';
                $passingScore = $empCourse->course->passingRates->where('exam_type','post')->where('attempt',$k)->first();
                if($passingScore) {
                    if($test->score>=$passingScore->score) {
                        $dateFinished = $test->created_at;
                        $passed = true;
                        break;
                    }
                } else
                    echo 'Ignoring post test #'.$k.'...<br>';
            }
            if($passed)
                App\EmployeeCourse::where('id',$empCourse->id)->update(['finished_date' => $dateFinished]);
            else
                App\EmployeeCourse::where('id',$empCourse->id)->update(['finished_date' => null]);
            echo 'Updating Record '.$empCourse->id.'...<br>';
        }
    });
});
