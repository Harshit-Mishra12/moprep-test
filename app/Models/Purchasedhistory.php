<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchasedhistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'batch_id',
        'course_id'
    ];

    public function course_data(){

        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }

    public function batch_data(){
    
        return $this->hasOne(Batch::class, 'id', 'batch_id');
    }
    
    public function user_data(){
    
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Batch::class, 'batch_id', 'id');
    }

}
