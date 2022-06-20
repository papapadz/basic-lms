<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'mysql_hris';
    public $table      = 'tbl_department';
}
