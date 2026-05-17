<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sbj_id');
            $table->integer('doc_id')->nullable();
            $table->string('name');
            $table->date('date');
            $table->string('pdf_file')->nullable();
            $table->string('mp4_file')->nullable();
            $table->string('pptx_file');
            $table->string('youtube_link')->nullable();
            $table->timestamps();

            $table->foreign('sbj_id')->references('id')->on('subjects')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}
