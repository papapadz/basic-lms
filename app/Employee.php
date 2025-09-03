<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    
    // protected $connection = 'mysql_hris';
    protected $primaryKey  = 'emp_id';
    // public $table      = 'tbl_employee';
    protected $casts = ['emp_id'=>'text']; 
    protected $guarded = [];

    public function user() {
        return $this->hasOne(User::class,'emp_id','emp_id');
    }

    public function position() {
        return $this->belongsTo(Position::class,'position_id');
    }

    public function department() {
        return $this->belongsTo(Department::class,'department_id')->with('division');
    }

    public function course() {
        return $this->hasMany(EmployeeCourse::class,'emp_id','emp_id');
    }
}
