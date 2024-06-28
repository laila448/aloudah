<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Branch_Manager extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'branch_managers';
    protected $guard = 'branch_manager';
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'branch_id',
        'gender',
        'mother_name',
        'date_of_birth',
        'manager_address',
        'vacations',
        'salary',
        'national_id',
        'employment_date',
        'device_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'branchmanager_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'branch_manager_id');
    }
}
