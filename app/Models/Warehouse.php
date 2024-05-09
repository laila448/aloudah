<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
       'warehouse_name',
       'address',
       'branch-id',
        'area',
        'notes',
         ];
         public function wmanager(){
             return $this->belongsTo(warehouse_manager::class, 'wmanager_id');
         }
         public function branch(){
            return $this->belongsTo(Branch::class, 'branch_id');
        }
         public function good(){
            return $this->hasMany(good::class, 'warehouse_id');
        }
     
         
}
