<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMapMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function courseMaster()
    {
        return $this->belongsTo(CourseMaster::class, 'course_master_id');
    }
}
