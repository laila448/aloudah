<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
   use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
      'name',
      'national_id',
      'email',
      'phone_number',
      'gender',
      'password',
       // 'user_id',   
        'mobile',
      'address',
        'address_detail',
       'notes',
        'added_by',
        
         ];
         public function user(){
            return $this->belongsTo(user::class, 'user_id');
         }
         public function complaint(){
            return $this->hasMany(complaint::class, 'customer_id');
         }
         public function shipping(){
            return $this->hasMany(shipping::class, 'customer_id');
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
