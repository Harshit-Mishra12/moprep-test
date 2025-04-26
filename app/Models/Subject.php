<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function course_details(){

        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }
    
    public function faq_data(){

        return $this->hasMany(\App\Models\FAQ::class, 'course_id', 'id');
    } 
    
    public function demo_video_data(){

        return $this->hasMany(\App\Models\DemoVideo::class, 'course_id', 'id');
    } 
    
    public function question_data(){

        return $this->hasMany(\App\Models\Question::class, 'course_id', 'id');
    }

    public function batches()
    {
        return $this->hasMany(\App\Models\Batch::class, 'course_id', 'id');
    }
}
