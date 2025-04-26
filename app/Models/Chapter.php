<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    
    public function course_details(){

        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }
    public function course_master_details(){

        return $this->hasOne(\App\Models\CourseMaster::class, 'id', 'course_master_id');
    }
    
    public function question_data(){

        return $this->hasOne(\App\Models\Question::class, 'subject_id', 'id');
    } 

    public function flag_data(){

        return $this->hasOne(\App\Models\Userflagquestion::class, 'subject_id', 'subject_id');
    }
}
