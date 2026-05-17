<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->integer('level_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('code');
            $table->string('password');
            $table->boolean('active', 0);
            $table->boolean('account_confirm', 0);
            $table->integer('set_number');
            $table->string('national_id');
            $table->boolean('graduated', 0);
            $table->boolean('can_see_result', 0);
            //$table->string('image')->default('default.png');
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
        Schema::dropIfExists('students');
    }
}
