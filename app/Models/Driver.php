<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $fillable = [
      'name',
      //'email',
      'phone_number',
      'gender',
      //'password',
       // 'user_id',
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
         public function trip(){
             return $this->hasMany(trip::class, 'truck_id');
         }
}
