<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;
    protected $fillable = [
       'number',
        'line',
        'created_by',
       'adding_data',
        'editing_by',
       'editing_date',
        'notes',
         ]; 
         public function trip(){
            return $this->hasMany(trip::class, 'truck_id');
        }
}
