<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'mysql_hris';
    protected $primaryKey = 'department_id';
    public $table      = 'tbl_department';
    
    public function employees() {
        return $this->hasMany(Employee::class,'department_id','department_id')->where('is_active','Y');
    }

    public function division() {
        return $this->belongsTo(Division::class,'division_id','division_id');
    }
}
