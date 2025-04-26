<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function faq_data(){

        return $this->hasMany(\App\Models\FAQ::class, 'course_id', 'id');
    } 
    
    public function demo_video_data(){

        return $this->hasMany(\App\Models\DemoVideo::class, 'course_id', 'id');
    } 
    
    public function question_data()
    {
        return $this->hasMany(\App\Models\Question::class, 'course_id', 'id')
            ->whereIn('course_master_id', function ($query) {
                $query->select('course_master_id')
                    ->from('course_map_masters')
                    ->whereColumn('course_map_masters.course_id', 'courses.id');
            });
    }


    public function batches(){
        return $this->hasMany(\App\Models\Batch::class, 'course_id', 'id');
    }

    public function CourseMaster()
    {
        return $this->hasOne(\App\Models\CourseMaster::class, 'id', 'course_master_id');
    }
}
