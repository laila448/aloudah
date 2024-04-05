<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
  use HasApiTokens, HasFactory, Notifiable;
  
    protected $fillable = [
      'name',
      'email',
      'phone_number',
      'gender',
      'password',
     
          ]; 
          public function user(){
            return $this->belongsTo(user::class, 'user_id');
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
