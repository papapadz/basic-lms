<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_id',10);
            $table->bigInteger('course_id');
            $table->bigInteger('module_id');
            $table->dateTime('finished_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_courses');
    }
}
