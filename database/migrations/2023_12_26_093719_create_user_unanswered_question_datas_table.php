<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserUnansweredQuestionDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_unanswered_question_datas', function (Blueprint $table) {
            $table->id();
            $table->enum('exam_type', ['revision', 'exam', 'mock']);
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('course_id')->constrained('courses', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->longText('question_data')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_unanswered_question_datas');
    }
}
