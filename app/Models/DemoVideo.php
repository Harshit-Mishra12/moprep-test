<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoVideo extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function course_details(){

        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }
}
