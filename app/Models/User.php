<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_number',
        'whatsapp_number',
        'mobile',
        'user_type',
        'referrer_id',
        'unique_id',
        'designation_id',
        'college',
        'state',
        'otp_status',
        'firebase_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function PurchaseHistory()
    {
        return $this->hasMany(\App\Models\Purchasedhistory::class, 'user_id', 'id');
    }

    public function batches()
    {
        return $this->belongsToMany(\App\Models\Batch::class, 'purchasedhistories', 'user_id', 'batch_id','id');
    }
}
