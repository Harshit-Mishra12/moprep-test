<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTableInUserExamData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::table('user_exam_data', function (Blueprint $table) {
            $table->dropColumn('exam_id');
        });

        Schema::table('user_exam_data', function (Blueprint $table) {
            $table->foreignId('exam_id')->nullable()->after('id')->constrained('user_exams', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_exam_data', function (Blueprint $table) {
            //
        });
    }
}
