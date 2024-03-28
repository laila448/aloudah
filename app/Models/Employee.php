<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{ 
    use HasFactory;
    protected $fillable = [
        'user_id',
        'branch_id',
        'grnder',
      ' mother_name',
        'gender',
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
}
