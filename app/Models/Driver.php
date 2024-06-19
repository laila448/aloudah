<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

<<<<<<< HEAD



class Driver extends Model

=======
class Driver extends Authenticatable implements JWTSubject
>>>>>>> a64dd25d254559f8d238ad2cc0e3b0562db8b4eb
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
<<<<<<< HEAD

=======
        'certificate',
        'device_token'
>>>>>>> a64dd25d254559f8d238ad2cc0e3b0562db8b4eb
          ]; 
         
          
          public function branch(){
            return $this->belongsTo(branch::class, 'branch_id');
         }
        
         public function getJWTIdentifier()
         {
             return $this->getKey();
         }
     
         public function getJWTCustomClaims()
         {
             return [];
         }
<<<<<<< HEAD

       
   

    protected $hidden = [
        'password', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

=======
>>>>>>> a64dd25d254559f8d238ad2cc0e3b0562db8b4eb
}
