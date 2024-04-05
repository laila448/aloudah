<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Branch_Manager extends  Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table = 'branch_managers';
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'user_id',
        'gender',
        
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
}
