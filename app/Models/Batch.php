<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function courseData(){
        return $this->hasOne(\App\Models\Course::class, 'id', 'course_id');
    }

    public function purchased_data(){
    
        return $this->hasOne(Purchasedhistory::class, 'batch_id', 'id');
    }

    public function PurchaseHistory()
    {
        return $this->hasMany(\App\Models\Purchasedhistory::class, 'batch_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'purchasedhistories', 'batch_id', 'user_id');
    }

}
