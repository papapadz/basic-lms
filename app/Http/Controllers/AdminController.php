<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Course;
use App\EmployeeCourse;
use App\Department;
use App\Division;
use App\Employee;
use App\Position;
use App\User;
use DB;

class AdminController extends Controller
{

    public function syncHRISData(Request $request) {
        try {
            if($request->has('_table') && ($request->_table='departments' || $request->_table='all')) {
                $divisions = DB::connection('mysql_hris')->table('tbl_division')->get();
                foreach($divisions as $div) {
                    $d = Division::firstOrCreate([
                        'id' => $div->division_id
                    ],[
                        'division' => $div->division
                    ]);
                }
                
                $departments = DB::connection('mysql_hris')->table('tbl_department')->where('division_id','>',0)->get();
                foreach($departments as $dept) {
                    $d = Department::firstOrCreate([
                        'id' => $dept->department_id
                    ],[
                        'department' => $dept->department,
                        'division_id' => $dept->division_id
                    ]);
                }
            }
            
            if($request->has('_table') && ($request->_table='employees' || $request->_table='all')) {
                $positions = DB::connection('mysql_hris')->table('tbl_position')->get();
                foreach($positions as $pos) {
                    $p = Position::firstOrCreate([
                        'id' => $pos->position_id
                    ],[
                        'position_title' => $pos->position_title,
                        'salary_grade' => $pos->salary_grade
                    ]);
                }

                $employees = DB::connection('mysql_hris')->table('tbl_employee')->where('emp_id','!=','000000')->get();
                foreach($employees as $emp) {
                    $e = Employee::firstOrCreate([
                        'emp_id' => $emp->emp_id
                    ],[
                        'firstname' => $emp->firstname,
                        'middlename' => $emp->middlename,
                        'lastname' => $emp->lastname,
                        'suffix' => $emp->suffix,
                        'extension' => $emp->extension,
                        'gender' => $emp->gender,
                        'date_hired' => $emp->date_hired,
                        'is_active' => $emp->is_active,
                        'department_id' => $emp->department_id,
                        'position_id' => $emp->position_id,
                        'email' => $emp->email
                    ]);

                }
            }

            if($request->has('_table') && ($request->_table='emails' || $request->_table='all')) {
                $employees = DB::connection('mysql_hris')->table('tbl_employee')->where('emp_id','!=','000000')->get();
                foreach($employees as $emp) {
                    $e = User::where('emp_id',$emp->emp_id)->first();
                    if($e) {
                        if (filter_var($emp->email, FILTER_VALIDATE_EMAIL)) {
                            $e->email = $emp->email;
                            $e->save();
                        }
                    }
                }
            }

            return 'ok';
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

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
                'firstname',
                'middlename',
                'lastname',
                'department'
            )
            ->join('employees','employees.emp_id','=','employee_courses.emp_id')
            ->join('departments','departments.id','=','employees.department_id')
            ->where('course_id',$request->course)
            ->whereDate('employee_courses.created_at','>=',$filterDate->startOfYear()->toDateString())
            ->whereDate('employee_courses.created_at','<=',$filterDate->endOfYear()->toDateString())
            ->whereDate('employees.date_hired','<=',$filterDate->endOfYear()->toDateString())
            ->where('employees.is_active','Y')
            ->where('departments.'.$qField,$qop,$qItem)
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
