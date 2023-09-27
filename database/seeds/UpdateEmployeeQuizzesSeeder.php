<?php

use Illuminate\Database\Seeder;
use App\EmployeeCourse;
use App\EmployeeQuiz;

class UpdateEmployeeQuizzesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employeeQuizzes = EmployeeQuiz::get();
        foreach($employeeQuizzes as $empQuiz) {
            $empCourse = EmployeeCourse::where('emp_id',$empQuiz->emp_id)->where('course_id',$empQuiz->emp_course_id)->withTrashed()->first();
            
            if($empCourse)
                $empQuiz->update(['emp_course_id' => $empCourse->id]);
        }
    }
}
