<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomClasses extends Model
{
    use HasFactory;

    public function course_details(){

        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }
    
    public function batch_data(){

        return $this->hasOne(\App\Models\Batch::class, 'id', 'batch_id');
    }
    
    public function topic_data(){

        return $this->hasOne(\App\Models\TopicMaterials::class, 'id', 'topic_id');
    }
}
