<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Course;
use App\EmployeeCourse;
use App\Department;
use App\Division;
use App\Employee;

class AdminController extends Controller
{
    public function getDashboardData(Request $request) {
        $filterDate = Carbon::now();
        $filterDate->year = $request->year;
        $totalCount = 0;
        $qField = 'department_id';
        $qItem = '';
        $qop = '=';
        if($request->has('division')) {
            if($request->division=='all') {
                $qop = '!=';
                $qItem = 6;
                $totalCount = Employee::where('is_active','Y')->whereDate('date_hired','<=',$filterDate->endOfYear()->toDateString())->count();
            } else {
                $qItem = $request->division;
                $totalCount = count(Division::find($request->division)->employees);
            }

            $qField = 'division_id';
        } else {
            $totalCount = count(Department::find($request->department)->employees);
            $qItem = $request->department;
        }
        
        $employeeCourses = EmployeeCourse::select()
            // ->with(['quiz' => function($q) {
            //     $q->where('quiz_type','post');
            // }])
            //->withTrashed()
            // ->with(['employee' => function($q) use ($request) {
            //     if($request->has('division')) 
            //         $q->where('department_id',$request->department);
            //     else
            //         $q->where('department_id',$request->department);
            // }])
            ->select(
                'employee_courses.*',
                'hris.tbl_employee.firstname',
                'hris.tbl_employee.middlename',
                'hris.tbl_employee.lastname',
                'hris.tbl_department.department'
            )
            ->join('hris.tbl_employee','hris.tbl_employee.emp_id','=','employee_courses.emp_id')
            ->join('hris.tbl_department','hris.tbl_department.department_id','=','hris.tbl_employee.department_id')
            ->where('course_id',$request->course)
            ->whereDate('employee_courses.created_at','>=',$filterDate->startOfYear()->toDateString())
            ->whereDate('employee_courses.created_at','<=',$filterDate->endOfYear()->toDateString())
            ->whereDate('hris.tbl_employee.date_hired','<=',$filterDate->endOfYear()->toDateString())
            ->where('hris.tbl_employee.is_active','Y')
            ->where('hris.tbl_department.'.$qField,$qop,$qItem)
            ->get();

        $total_enrolled = count($employeeCourses);
        $total_finished = count($employeeCourses->where('finished_date','!=',null));
        // $arrEmp = [];
        // $list = [];
        // $total_finished = 0;
        // foreach($employeeCourses as $empCourse) {
        //     if($empCourse->finished_date)
        //         $total_finished++;
        //     if($empCourse->employee) {
        //         if($request->has('division')) {
        //             if($empCourse->employee->department->division_id==$request->division && !in_array($empCourse->emp_id, $arrEmp)) {
        //                 $total_enrolled++;
        //                 array_push($arrEmp, $empCourse->emp_id);
        //                 array_push($list,$empCourse);
        //             }
        //         } else {
        //             if($empCourse->employee->department && $empCourse->employee->department->department_id==$request->department && !in_array($empCourse->emp_id, $arrEmp)) {
        //                 $total_enrolled++;
        //                 array_push($arrEmp, $empCourse->emp_id);
        //                 array_push($list,$empCourse);
        //             }
        //         }
        //     }
        // }
        return array(
            'total_finished' => $total_finished,
            'total_enrolled' => $total_enrolled,
            'employee_count' => $totalCount,
            'percentage' => number_format(($total_enrolled/$totalCount)*100,2),
            'percentage_finished' => $total_enrolled>0 ? number_format(($total_finished/$total_enrolled)*100,2) : number_format(0,2),
            'list' => $employeeCourses
        );
    }
}
