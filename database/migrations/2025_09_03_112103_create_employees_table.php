<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('emp_id',20)->primary();
            $table->string('firstname',50);
            $table->string('middlename',50)->nullable();
            $table->string('lastname',50);
            $table->string('suffix',50)->nullable();
            $table->string('extension',50)->nullable();
            $table->enum('gender',['M','F'])->default('M');
            $table->date('date_hired');
            $table->unsignedBigInteger('department_id');   
            $table->unsignedBigInteger('position_id');
            $table->string('is_active',1)->default('Y');
            $table->string('email',100);
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('position_id')->references('id')->on('positions');
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
        Schema::dropIfExists('employees');
    }
}
