<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function faq_data(){

        return $this->hasMany(\App\Models\FAQ::class, 'course_master_id', 'id');
    } 
    
    public function demo_video_data(){

        return $this->hasMany(\App\Models\DemoVideo::class, 'course_master_id', 'id');
    } 
    
    public function question_data(){

        return $this->hasMany(\App\Models\Question::class, 'course_master_id', 'id');
    }

    public function batches()
    {
        return $this->hasMany(\App\Models\Batch::class, 'course_master_id', 'id');
    }

    public function subjects()
    {
        return $this->hasOne(\App\Models\Subject::class, 'course_master_id', 'id');
    }
}
