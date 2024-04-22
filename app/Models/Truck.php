<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;
    protected $fillable = [
      'branch_id',
      'number',
        'line',
        'created_by',  
       'adding_data',
        'editing_by',
       'editing_date',
        'notes',
         ]; 

         protected $hidden = ['created_at','updated_at'];

         public function trips()
         {
            return $this->hasMany(Trip::class,'truck_id');
         } 
         public function branch()
             {
                return $this->belongsTo(Branch::class);
             }
       
}
