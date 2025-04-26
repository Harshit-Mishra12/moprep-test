<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function course_details(){

        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }
    
    public function course(){

        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }
    
    public function chapter_data(){

        return $this->hasOne(\App\Models\Chapter::class, 'id', 'chapter_id');
    }

    public function flagQuestions(){

        return $this->hasMany(\App\Models\Userflagquestion::class, 'question_id', 'id');
    }
    
    public function userExamData(){

        return $this->hasMany(\App\Models\UserExamData::class, 'question_id', 'id');
    }
    
    public function user_exam_data(){

        return $this->hasOne(\App\Models\UserExamData::class, 'question_id', 'id');
    }
}
