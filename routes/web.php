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
//Route::get('/home', 'HomeController@index')->name('home');
Route::middleware('auth')->group(function() {
    Route::get('/', 'CourseController@homepage')->name('homepage');

    Route::get('course/{course}', 'CourseController@course')->name('course');
    Route::get('course/{course}/{module}', 'CourseController@module')->name('module');
    Route::get('/{course}/summary', 'CourseController@summary')->name('summary');
    Route::get('/admin', 'CourseController@admin')->middleware('admin')->name('admin');

    Route::resource('quiz', 'QuizController');
    Route::post('quiz/submit', 'QuizController@submitQuiz');
    Route::get('course/done/{course_id}','CourseController@done');
    Route::get('course/get/certificate/{id}','QuizController@getCertificate')->name('get-certificate'); 
});


Route::prefix('admin')->name('admin.')->middleware('auth','admin')->group(function (){
    Route::resource('courses', 'CourseController');
    Route::resource('modules', 'ModuleController');
    Route::post('course/set/active','CourseController@setActive');
    Route::get('results','QuizController@index');
    Route::post('results/verify/{course_id}/{emp_id}','QuizController@verify')->name('results.verify');
});
