<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoomClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses','id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('batch_id')->constrained('batches','id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('topic_id')->constrained('topic_materials','id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->string('start_time');
            $table->string('duration');
            $table->string('meeting_id');
            $table->string('join_url');
            $table->string('start_url');
            $table->string('password');
            $table->string("sort_order");
			$table->enum('status',['0','1'])->default('0')->comment('0=>Pending,1=>Active');
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
        Schema::dropIfExists('zoom_classes');
    }
}
