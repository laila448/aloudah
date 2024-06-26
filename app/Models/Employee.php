<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Employee extends Authenticatable implements JWTSubject
{ 
   use HasApiTokens, HasFactory, Notifiable , SoftDeletes;
  
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
        'device_token'

        
         ];
         public function branch(){
            return $this->belongsTo(Branch::class, 'branch_id');
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
