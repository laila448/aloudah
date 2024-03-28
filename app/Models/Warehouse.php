<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'wmanager',
        'address',
         'phone',
         ];
         public function wmanager(){
             return $this->belongsTo(warehouse_manager::class, 'wmanager_id');
         }
         public function good(){
            return $this->hasMany(good::class, 'warehouse_id');
        }
     
         
}
