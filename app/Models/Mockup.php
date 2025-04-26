<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mockup extends Model
{
    use HasFactory;

    public function courseMaster(){

        return $this->hasOne(\App\Models\CourseMaster::class, 'id', 'course_master_id');
    }
}
