<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('course_name')->nullable();
            $table->string('course_slug')->unique()->nullable();
            $table->string('code')->unique();
            $table->longText('course_description');
            $table->longText('post_notes')->nullable();
            $table->longText('content');
            $table->string('course_image')->nullable();
            $table->string('course_cert',100);
            $table->boolean('needs_verification')->default(false);
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
        Schema::dropIfExists('courses');
    }
}
