<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_quizzes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_id',10);
            $table->bigInteger('course_id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('quiz_type');
            $table->integer('score');
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
        Schema::dropIfExists('employee_quizzes');
    }
}
