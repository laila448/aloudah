<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Driver extends Model
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
    ];

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
}
