<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Employee;
use App\EmployeeCourse;

class UserController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        $courses = EmployeeCourse::where('emp_id',$user->emp_id)->withTrashed()->get();
        
        return view('users.index')->with([
            'user' => $user,
            'courses' => $courses
        ]);
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
        $returnVal = [
            'code' => 0,
            'message' => ''
        ];
        
        if($request->has('old_password')) {

            $validator = Validator::make($request->all(), [
                'old_password' => 'required|min:6',
                'new_password' => 'required|min:6',
            ],[
                'old_password.min' => 'Passwords must be at least 6 characters'
            ]);
            
            if($validator->fails()) {
                $returnVal['code'] = 0;
                $returnVal['message'] = $validator->errors('old_password')->first();
            } else {
                $user = User::find($id);
                if(Hash::check($request->input('old_password',$user),$user->password)) {
                    $user->password = Hash::make($request->input('new_password'));
                    $user->save();

                    $returnVal['code'] = 1;
                    $returnVal['message'] = 'Password has been updated';
                } else {
                    $returnVal['code'] = 0;
                    $returnVal['message'] = 'Password is incorrect!';
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:users'
            ]);
            
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users'
            ]);
            
            if($validator->fails()) {
                $returnVal['code'] = 0;
                $returnVal['message'] = $validator->errors('email')->first();
            } else {
                User::where('id',$id)->update([
                    'email' => $request->input('email')
                ]);
                
                $employee = Employee::find(User::find($id)->emp_id);
                $employee->email = $request->input('email');
                $employee->save();

                $returnVal['code'] = 1;
                $returnVal['message'] = 'Email has been updated';
            }
        }
        // User::update([
        //     'email' => $request->email
        // ])->where('id',$id);
        // return null;
        return $returnVal;
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
}
