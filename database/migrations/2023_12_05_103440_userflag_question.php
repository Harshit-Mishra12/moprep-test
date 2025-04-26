<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserflagQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userflagquestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('course_id')->constrained('courses', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('question_id')->constrained('questions', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::table('userflagquestions', function (Blueprint $table) {
            //
        });
    }
}
