<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public function CourseMaster()
    {
        return $this->hasOne(\App\Models\CourseMaster::class, 'id', 'course_master_id');
    }
}
