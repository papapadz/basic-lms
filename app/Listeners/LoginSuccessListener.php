<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Employee;

class LoginSuccessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $employee = DB::connection('mysql_hris')->table('tbl_employee')->where('emp_id',$user->emp_id)->first();
        if($employee) {
            $user->email = $employee->email;
            if($user->isDirty('email'))
                $user->save();
            
            $lmsEmployee = Employee::where('emp_id',$user->emp_id)->first();
            $lmsEmployee->firstname = $employee->firstname;
            $lmsEmployee->lastname = $employee->lastname;
            $lmsEmployee->middlename = $employee->middlename;
            $lmsEmployee->position_id = $employee->position_id;
            $lmsEmployee->department_id = $employee->department_id;
            $lmsEmployee->is_active = $employee->is_active;
            $lmsEmployee->email = $employee->email;
            $lmsEmployee->date_hired = $employee->date_hired;
            if($lmsEmployee->isDirty())
                $lmsEmployee->save();
        }
    }
}
