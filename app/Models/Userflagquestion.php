<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userflagquestion extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'course_id',
        'question_id'
    ];
}
