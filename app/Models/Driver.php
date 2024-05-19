<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
      'name',
      'national_id',
      //'email',
      'phone_number',
      'gender',
      //'password',
       // 'user_id',
        'branch_id',
      'mother_name',
       'birth_date',
       'birth_place',
       'mobile',
        'address',
        'certificate_type',
       // 'national_number',
       // 'vacations',
        'salary',
        'rank',
      //  'rewards',
        'employment_date',
        'resignation_date',
        'manager_name',
          ]; 
          protected $hidden = ['created_at','updated_at'];
          
          public function user(){
            return $this->belongsTo(user::class, 'user_id');
         }
         public function trips(){
            //  return $this->hasMany(trip::class, 'truck_id');
            return $this->hasMany(trip::class, 'driver_id');
         }
}
