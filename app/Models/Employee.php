<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Employee extends Authenticatable implements JWTSubject
{ 
   use HasApiTokens, HasFactory, Notifiable;
  
    protected $fillable = [
      'name',
      'email',
      'phone_number',
      'gender',
      'password',
        //'user_id',
        'branch_id',
        'grnder',
      ' mother_name',
       'date_of_birth',
        'address',
        'national_number',
        'vacations',
        'salary',
        'rewards',
        'employment_date',
        'resignation_date',
        'manager_name',

        
         ];
         public function user(){
            return $this->belongsTo(user::class, 'user_id');
         }
         public function rating(){
          return $this->hasMany(rating::class, 'employee_id');
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
