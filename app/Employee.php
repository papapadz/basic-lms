<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    
    protected $connection = 'mysql_hris';
    protected $primaryKey  = 'emp_id';
    public $table      = 'tbl_employee';
    
    public function user() {
        return $this->hasOne(User::class,'emp_id','emp_id');
    }
}
