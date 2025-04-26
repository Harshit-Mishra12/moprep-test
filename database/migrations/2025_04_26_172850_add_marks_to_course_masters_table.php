<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarksToCourseMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_masters', function (Blueprint $table) {
            $table->decimal('positive_mark', 8, 2)->default(0)->after('slug');
            $table->decimal('negative_mark', 8, 2)->default(0)->after('positive_mark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_masters', function (Blueprint $table) {
            $table->dropColumn(['positive_mark', 'negative_mark']);
        });
    }
}
