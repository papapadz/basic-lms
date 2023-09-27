<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $connection = 'mysql_hris';
    protected $primaryKey = 'division_id';
    public $table      = 'tbl_division';
    
    public function departments() {
        return $this->hasMany(Department::class,'division_id','division_id');
    }

    public function employees() {
        return $this->hasManyThrough(
            Employee::class,
            Department::class,
            'division_id',
            'department_id'
        )->where('is_active','Y');
    }
}
