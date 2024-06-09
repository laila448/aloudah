<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'national_id',
        'email',
        'phone_number',
        'gender',
        'password',
        'branch_id',
        'mother_name',
        'birth_date',
        'birth_place',
        'mobile',
        'address',
        'salary',
        'rank',
        'employment_date',
        'resignation_date',
        'manager_name',
        'certificate',
        'device_token'
          ]; 
          protected $hidden = ['created_at','updated_at'];
          
          public function branch(){
            return $this->belongsTo(branch::class, 'branch_id');
         }
         public function trips(){
            //  return $this->hasMany(trip::class, 'truck_id');
            return $this->hasMany(trip::class, 'driver_id');
         }

         public function getJWTIdentifier()
         {
             return $this->getKey();
         }
     
         public function getJWTCustomClaims()
         {
             return [];
         }
}
