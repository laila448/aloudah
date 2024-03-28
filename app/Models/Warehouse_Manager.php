<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse_Manager extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'grnder',
        
         ];
         public function user(){
            return $this->belongsTo(user::class, 'user_id');
         }
}
