<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_exams', function (Blueprint $table) {
            $table->integer('pause_count')->default(0)->after('question_limit');
            $table->longText('answer_data')->nullable()->after('pause_count');
            $table->string('pause_timing')->nullable()->after('answer_data');
            $table->integer('pause_question')->default(0)->after('pause_timing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_exams', function (Blueprint $table) {
            //
        });
    }
}
