<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',   
        'mobile',
      ' address',
        'address_detail',
       'notes',
        'passwords',
        'added_by',
        
         ];
         public function user(){
            return $this->belongsTo(user::class, 'user_id');
         }
         public function complaint(){
            return $this->hasMany(complaint::class, 'customer_id');
         }
         public function shipping(){
            return $this->hasMany(shipping::class, 'customer_id');
         }
}
