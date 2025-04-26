<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExam extends Model
{
    use HasFactory;

    public function user_data()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function chapters_data()
    {
        return $this->belongsToMany(Chapter::class,'chapters', 'id', 'chapter_id');
    }
}
