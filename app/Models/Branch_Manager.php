<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Branch_Manager extends  Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'branch_managers';
    protected $guard = 'branch_manager';
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'branch_id',
        //'user_id',
        'gender',
        'mother_name',
        'date_of_birth',
        'address',
       'national_number',
       'vacations',
       'salary',
       'rewards',
        'employment_date',
        'resignation_date',
         ];

         public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    
         public function user(){
            return $this->belongsTo(user::class, 'user_id');
         }

         public function branch(){
            return $this->belongsTo(Branch::class , 'branch_id');
         }
}
